<?php

/**
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2021 Christoph M. Becker
 *
 * This file is part of Cryptographp_XH.
 *
 * Cryptographp_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Cryptographp_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Cryptographp_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Cryptographp\Model;

use GdImage;

class VisualCaptcha
{
    /** @var string */
    private $imageFolder;

    /** @var string */
    private $fontFolder;

    /** @var array<string,string> */
    private $config;

    /** @param array<string,string> $config */
    public function __construct(string $imageFolder, string $fontFolder, array $config)
    {
        $this->imageFolder = $imageFolder;
        $this->fontFolder = realpath($fontFolder) . "/"; // ZTS builds won't find relative paths
        $this->config = $config;
    }

    public function createImage(string $code): ?string
    {
        $result = $this->precalculate($code);
        if ($result === null) {
            return null;
        }
        [$word, $xOffset] = $result;

        $image = imagecreatetruecolor(...$this->dimensions());
        if ($image === false) {
            return null;
        }
        $charColor = $this->allocateColor(
            $image,
            (int) $this->config['char_rgb_red'],
            (int) $this->config['char_rgb_green'],
            (int) $this->config['char_rgb_blue'],
            (int) $this->config['char_clear']
        );
        $backgroundColor = $this->allocateColor(
            $image,
            (int) $this->config['bg_rgb_red'],
            (int) $this->config['bg_rgb_green'],
            (int) $this->config['bg_rgb_blue']
        );
        if (!$this->paintBackground($image, $backgroundColor)) {
            return null;
        }
        if ($this->config['noise_above']) {
            if (!$this->paintCharacters($image, $word, $xOffset, $charColor)) {
                return null;
            }
            if (!$this->paintNoise($image, $charColor, $backgroundColor)) {
                return null;
            }
        } else {
            if (!$this->paintNoise($image, $charColor, $backgroundColor)) {
                return null;
            }
            if (!$this->paintCharacters($image, $word, $xOffset, $charColor)) {
                return null;
            }
        }
        if ($this->config['bg_frame']) {
            if (!$this->paintFrame($image)) {
                return null;
            }
        }
        if ($this->config['crypt_gray_scale']) {
            if (!imagefilter($image, IMG_FILTER_GRAYSCALE)) {
                return null;
            }
        }
        if ($this->config['crypt_gaussian_blur']) {
            if (!imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR)) {
                return null;
            }
        }
        return $this->imageData($image);
    }

    /** @return array{list<Glyph>,int} */
    private function precalculate(string $code): ?array
    {
        $image = imagecreatetruecolor(...$this->dimensions());
        if ($image === false) {
            return null;
        }
        $blank = $this->allocateColor($image, 255, 255, 255);
        $black = $this->allocateColor($image, 0, 0, 0);
        if (!imagefill($image, 0, 0, $blank)) {
            return null;
        }
        $word = [];
        $x = 10;
        $fonts = explode(";", $this->config['char_fonts']);
        for ($i = 0; $i < strlen($code); $i++) {
            $font = $fonts[$this->randomFont(count($fonts))];
            $angle = $this->randomAngle((int) $this->config['char_angle_max']);

            $char = $code[$i];

            $size = $this->randomCharSize(...$this->charSizeRange());
            $bbox = imagettfbbox($size, $angle, $this->fontFolder . $font, $char);
            if ($bbox === false) {
                return null;
            }
            $min = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $max = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            assert(is_int($min) && is_int($max));
            [, $height] = $this->dimensions();
            $delta = $height - $max + $min;
            $y = intdiv($delta, 2) + abs($min) - 1;
            if ($this->config['char_displace']) {
                $y += $this->randomDisplacement(-intdiv($delta, 2), intdiv($delta, 2));
            }
            if (!imagettftext($image, $size, $angle, $x, $y, $black, $this->fontFolder . $font, $char)) {
                return null;
            }
            $word[] = new Glyph($font, $angle, $char, $size, $y);
            $x += $this->config['char_space'];
        }
        [$width] = $this->dimensions();
        $textWidth = $this->calculateTextWidth($image, $blank);
        $xOffset = intdiv($width - $textWidth, 2);
        return [$word, $xOffset];
    }

    /** @param GdImage $image */
    private function calculateTextWidth($image, int $blank): int
    {
        [$width] = $this->dimensions();
        $xbegin = 0;
        for ($x = 0; $x < $width && !$xbegin; $x++) {
            $xbegin = $this->scanColumn($image, $x, $blank);
        }
        $xend = 0;
        for ($x = $width - 1; $x > 0 && !$xend; $x--) {
            $xend = $this->scanColumn($image, $x, $blank);
        }
        return $xend - $xbegin;
    }

    /** @param GdImage $image */
    private function scanColumn($image, int $x, int $blank): int
    {
        [, $height] = $this->dimensions();
        for ($y = 0; $y < $height; $y++) {
            if (imagecolorat($image, $x, $y) !== $blank) {
                return $x;
            }
        }
        return 0;
    }

    private function findBackgroundImage(): ?string
    {
        if (!$this->config['bg_image']) {
            return null;
        }
        $filename = $this->imageFolder . $this->config['bg_image'];
        if (is_file($filename)) {
            return $filename;
        }
        if (!is_dir($filename)) {
            return null;
        }
        $entries = scandir($filename);
        if ($entries === false) {
            return null;
        }
        $files = array_values(array_filter($entries, function ($basename) {
            return (bool) preg_match('/\.(gif|jpg|png)$/', $basename);
        }));
        return $filename . '/' . $files[$this->randomBackgroundImage(count($files))];
    }

    /** @param GdImage $image */
    private function getNoiseColor($image, int $charColor, int $backgroundColor): int
    {
        switch ($this->config['noise_color']) {
            case 1:
                return $charColor;
            case 2:
                return $backgroundColor;
            default:
                return $this->allocateColor($image, ...$this->randomNoiseColor());
        }
    }

    /** @param GdImage $image */
    private function paintBackground($image, int $color): bool
    {
        $bgimg = $this->findBackgroundImage();
        if ($bgimg === null) {
            return $this->paintStaticBackground($image, $color);
        }
        $imagesize = getimagesize($bgimg);
        if ($imagesize === false) {
            return $this->paintStaticBackground($image, $color);
        }
        [$getwidth, $getheight, $gettype] = $imagesize;
        switch ($gettype) {
            case IMAGETYPE_GIF:
                $imgread = imagecreatefromgif($bgimg);
                break;
            case IMAGETYPE_JPEG:
                $imgread = imagecreatefromjpeg($bgimg);
                break;
            case IMAGETYPE_PNG:
                $imgread = imagecreatefrompng($bgimg);
                break;
            default:
                $imgread = false;
        }
        if ($imgread === false) {
            return $this->paintStaticBackground($image, $color);
        }
        [$width, $height] = $this->dimensions();
        if (!imagecopyresampled($image, $imgread, 0, 0, 0, 0, $width, $height, $getwidth, $getheight)) {
            return false;
        }
        return true;
    }

    /** @param GdImage $image */
    private function paintStaticBackground($image, int $color): bool
    {
        if (!imagefill($image, 0, 0, $color)) {
            return false;
        }
        if ($this->config['bg_clear']) {
            imagecolortransparent($image, $color);
        }
        return true;
    }

    /**
     * @param GdImage $image
     * @param list<Glyph> $word
     */
    private function paintCharacters($image, array $word, int $xOffset, int $color): bool
    {
        $x = $xOffset;
        foreach ($word as $glyph) {
            if ($this->config['char_color_random']) {
                $ink = $this->chooseRandomColor($image);
            } else {
                $ink = $color;
            }
            $font = $this->fontFolder . $glyph->font();
            if (!imagettftext($image, $glyph->size(), $glyph->angle(), $x, $glyph->y(), $ink, $font, $glyph->char())) {
                return false;
            }
            $x += $this->config['char_space'];
        }
        return true;
    }

    /** @param GdImage $image */
    private function chooseRandomColor($image): int
    {
        do {
            [$red, $green, $blue] = $this->randomCharColor();
        } while (!$this->isValidRandomColor($red + $green + $blue));
        return $this->allocateColor($image, $red, $green, $blue, (int) $this->config['char_clear']);
    }

    private function isValidRandomColor(int $color): bool
    {
        switch ($this->config['char_color_random_level']) {
            case 1: // very dark
                return $color < 200;
            case 2: // dark
                return $color < 400;
            case 3: // light
                return $color > 500;
            case 4: // very light
                return $color > 650;
            default:
                return true;
        }
    }

    /** @param GdImage $image */
    private function paintNoise($image, int $charColor, int $backgroundColor): bool
    {
        $nbpx = $this->randomPointCount(...$this->noisePixelRange());
        $nbline = $this->randomLineCount(...$this->noiseLineRange());
        $nbcircle = $this->randomCircleCount(...$this->noiseCircleRange());
        for ($i = 0; $i < $nbpx; $i++) {
            [$x, $y] = $this->randomPoint(...$this->dimensions());
            $color = $this->getNoiseColor($image, $charColor, $backgroundColor);
            if (!imagesetpixel($image, $x, $y, $color)) {
                return false;
            }
        }
        if (!imagesetthickness($image, (int) $this->config['noise_brush_size'])) {
            return false;
        }
        for ($i = 0; $i < $nbline; $i++) {
            [$x1, $y1, $x2, $y2] = $this->randomLine(...$this->dimensions());
            $color = $this->getNoiseColor($image, $charColor, $backgroundColor);
            if (!imageline($image, $x1, $y1, $x2, $y2, $color)) {
                return false;
            }
        }
        for ($i = 0; $i < $nbcircle; $i++) {
            [$x, $y, $diameter] = $this->randomCircle(...$this->dimensions());
            $color = $this->getNoiseColor($image, $charColor, $backgroundColor);
            if (!imagearc($image, $x, $y, $diameter, $diameter, 0, 359, $color)) {
                return false;
            }
        }
        return true;
    }

    /** @param GdImage $image */
    private function paintFrame($image): bool
    {
        $color = $this->allocateColor(
            $image,
            intdiv(((int) $this->config['bg_rgb_red'] * 3 + (int) $this->config['char_rgb_red']), 4),
            intdiv(((int) $this->config['bg_rgb_green'] * 3 + (int) $this->config['char_rgb_green']), 4),
            intdiv(((int) $this->config['bg_rgb_blue'] * 3 + (int) $this->config['char_rgb_blue']), 4)
        );
        [$width, $height] = $this->dimensions();
        if (!imagerectangle($image, 0, 0, $width - 1, $height - 1, $color)) {
            return false;
        }
        return true;
    }

    public function createErrorImage(string $text): ?string
    {
        $text = (string) preg_replace('/(?=\s)(.{1,15})(?:\s|$)/u', "\$1\n", $text);
        $lines = explode("\n", $text);
        $font = "{$this->fontFolder}DejaVuSans.ttf";
        $fontsize = 12;
        $padding = 5;
        $bbox = imagettfbbox($fontsize, 0, $font, $text);
        if ($bbox === false) {
            return null;
        }
        $width = $bbox[2] - $bbox[0] + 1 + 2 * $padding;
        $height = $bbox[1] - $bbox[7] + 1 + 2 * $padding;
        $bbox = imagettfbbox($fontsize, 0, $font, $lines[0]);
        if ($bbox === false) {
            return null;
        }
        $img = imagecreatetruecolor($width, $height);
        if ($img === false) {
            return null;
        }
        $bg = $this->allocateColor($img, 255, 255, 255);
        $fg = $this->allocateColor($img, 192, 0, 0);
        if (!imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $bg)) {
            return null;
        }
        if (!imagettftext($img, $fontsize, 0, $padding, $bbox[1] - $bbox[7] + 1, $fg, $font, $text)) {
            return null;
        }
        return $this->imageData($img);
    }

    /** @return array{int,int} */
    private function dimensions(): array
    {
        return [(int) $this->config['crypt_width'], (int) $this->config['crypt_height']];
    }

    /** @return array{int,int} */
    private function charSizeRange(): array
    {
        return [(int) $this->config['char_size_min'], (int) $this->config['char_size_max']];
    }

    /** @return array{int,int} */
    private function noisePixelRange(): array
    {
        return [(int) $this->config['noise_pixel_min'], (int) $this->config['noise_pixel_max']];
    }

    /** @return array{int,int} */
    private function noiseLineRange(): array
    {
        return [(int) $this->config['noise_line_min'], (int) $this->config['noise_line_max']];
    }

    /** @return array{int,int} */
    private function noiseCircleRange(): array
    {
        return [(int) $this->config['noise_circle_min'], (int) $this->config['noise_circle_max']];
    }

    /** @param GdImage $image */
    private function allocateColor($image, int $red, int $green, int $blue, int $alpha = 0): int
    {
        $color = imagecolorallocatealpha($image, $red, $green, $blue, $alpha);
        assert($color !== false);
        return $color;
    }

    /** @param GdImage $image */
    private function imageData($image): ?string
    {
        ob_start();
        $res = imagepng($image);
        $output = ob_get_clean();
        if (!$res || $output === false) {
            return null;
        }
        return $output;
    }

    /** @codeCoverageIgnore */
    protected function randomFont(int $count): int
    {
        return random_int(0, $count - 1);
    }

    /** @codeCoverageIgnore */
    protected function randomAngle(int $max): int
    {
        return random_int(1, 2) == 1
            ? random_int(0, $max)
            : random_int(360 - $max, 360);
    }

    /** @codeCoverageIgnore */
    protected function randomCharSize(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /** @codeCoverageIgnore */
    protected function randomDisplacement(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /** @codeCoverageIgnore */
    protected function randomBackgroundImage(int $count): int
    {
        return random_int(0, $count - 1);
    }

    /**
     * @return array{int,int,int}
     * @codeCoverageIgnore
     */
    protected function randomNoiseColor(): array
    {
        return [random_int(0, 255), random_int(0, 255), random_int(0, 255)];
    }

    /**
     * @return array{int,int,int}
     * @codeCoverageIgnore
     */
    protected function randomCharColor(): array
    {
        return [random_int(0, 255), random_int(0, 255), random_int(0, 255)];
    }

    /** @codeCoverageIgnore */
    protected function randomPointCount(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /** @codeCoverageIgnore */
    protected function randomLineCount(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /** @codeCoverageIgnore */
    protected function randomCircleCount(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /**
     * @return array{int,int}
     * @codeCoverageIgnore
     */
    protected function randomPoint(int $width, int $height): array
    {
        return [random_int(0, $width - 1), random_int(0, $height - 1)];
    }

    /**
     * @return array{int,int,int,int}
     * @codeCoverageIgnore
     */
    protected function randomLine(int $width, int $height): array
    {
        return [
            random_int(0, $width - 1),
            random_int(0, $height - 1),
            random_int(0, $width - 1),
            random_int(0, $height - 1)
        ];
    }

    /**
     * @return array{int,int,int}
     * @codeCoverageIgnore
     */
    protected function randomCircle(int $width, int $height): array
    {
        $diameter = random_int(5, intdiv($width, 3));
        return [random_int(0, $width - 1), random_int(0, $height - 1), $diameter];
    }
}
