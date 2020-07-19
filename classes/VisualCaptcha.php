<?php

/**
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2017 Christoph M. Becker
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

namespace Cryptographp;

use stdClass;

class VisualCaptcha
{
    /**
     * @var resource
     */
    private $image;

    /**
     * @var object[]
     */
    private $word;

    /**
     * @var int
     */
    private $ink;

    /**
     * @var int
     */
    private $bg;

    /**
     * @var int
     */
    private $xOffset;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $imageFolder;

    /**
     * @var string
     */
    private $fontFolder;

    /**
     * @var array
     */
    private $fonts;

    /**
     * @var array
     */
    private $config;

    public function __construct()
    {
        global $pth, $plugin_cf;

        $this->imageFolder = $pth['folder']['images'];
        $this->fontFolder = realpath("{$pth['folder']['plugins']}cryptographp/fonts") . '/';
        $this->config = $plugin_cf['cryptographp'];
        $this->fonts = explode(';', $this->config['char_fonts']);
    }

    /**
     * @param string $code
     * @return resource
     */
    public function createImage($code)
    {
        $this->code = $code;
        $this->precalculate();

        $this->image = imagecreatetruecolor($this->config['crypt_width'], $this->config['crypt_height']);
        $this->paintBackground();
        if ($this->config['noise_above']) {
            $this->paintCharacters();
            $this->paintNoise();
        } else {
            $this->paintNoise();
            $this->paintCharacters();
        }
        if ($this->config['bg_frame']) {
            $this->paintFrame();
        }
        if ($this->config['crypt_gray_scale']) {
            imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        }
        if ($this->config['crypt_gaussian_blur']) {
            imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this->image;
    }

    private function precalculate()
    {
        $image = imagecreatetruecolor($this->config['crypt_width'], $this->config['crypt_height']);
        $blank = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $blank);

        $x = 10;
        for ($i = 0; $i < strlen($this->code); $i++) {
            $char = new stdClass;
            $char->font =  $this->fonts[mt_rand(0, count($this->fonts) - 1)];
            $char->angle = mt_rand(1, 2) == 1
                ? mt_rand(0, $this->config['char_angle_max'])
                : mt_rand(360 - $this->config['char_angle_max'], 360);

            $char->element = $this->code[$i];

            $char->size = mt_rand($this->config['char_size_min'], $this->config['char_size_max']);
            $font = $this->fontFolder . $char->font;
            $bbox = imagettfbbox($char->size, $char->angle, $font, $char->element);
            $min = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $max = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $delta = $this->config['crypt_height'] - $max + $min;
            $char->y = $delta / 2 + abs($min) - 1;
            if ($this->config['char_displace']) {
                $char->y += mt_rand(-intval($delta / 2), intval($delta / 2));
            }
            imagettftext(
                $image,
                $char->size,
                $char->angle,
                $x,
                $char->y,
                $black,
                $font,
                $char->element
            );
            $this->word[] = $char;
            $x += $this->config['char_space'];
        }

        $width = $this->calculateTextWidth($image, $blank);
        $this->xOffset = round(($this->config['crypt_width'] - $width) / 2);
        imagedestroy($image);
    }

    /**
     * @param resource $image
     * @param int $blank
     * @return int
     */
    private function calculateTextWidth($image, $blank)
    {
        $width = $this->config['crypt_width'];
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

    /**
     * @param resource $image
     * @param int $x
     * @param int $blank
     * @return int
     */
    private function scanColumn($image, $x, $blank)
    {
        for ($y = 0; $y < $this->config['crypt_height']; $y++) {
            if (imagecolorat($image, $x, $y) != $blank) {
                return $x;
            }
        }
        return 0;
    }
    
    /**
     * @return string
     */
    private function findBackgroundImage()
    {
        if ($this->config['bg_image']) {
            $filename = $this->imageFolder . $this->config['bg_image'];
            if (is_dir($filename)) {
                $files = array_values(array_filter(scandir($filename), function ($basename) {
                    return preg_match('/\.(gif|jpg|png)$/', $basename);
                }));
                return $filename . '/' . $files[mt_rand(0, count($files) - 1)];
            } elseif (is_file($filename)) {
                return $filename;
            }
        }
        return false;
    }

    /**
     * @return int
     */
    private function getNoiseColor()
    {
        switch ($this->config['noise_color']) {
            case 1:
                return $this->ink;
            case 2:
                return $this->bg;
            default:
                return imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        }
    }

    /**
     * @return void
     */
    private function paintBackground()
    {
        $bgimg = $this->findBackgroundImage();
        if ($bgimg) {
            list($getwidth, $getheight, $gettype) = getimagesize($bgimg);
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
            }
            imagecopyresampled(
                $this->image,
                $imgread,
                0,
                0,
                0,
                0,
                $this->config['crypt_width'],
                $this->config['crypt_height'],
                $getwidth,
                $getheight
            );
            imagedestroy($imgread);
        } else {
            $this->bg = imagecolorallocate(
                $this->image,
                $this->config['bg_rgb_red'],
                $this->config['bg_rgb_green'],
                $this->config['bg_rgb_blue']
            );
            imagefill($this->image, 0, 0, $this->bg);
            if ($this->config['bg_clear']) {
                imagecolortransparent($this->image, $this->bg);
            }
        }
    }

    private function paintCharacters()
    {
        $this->ink = imagecolorallocatealpha(
            $this->image,
            $this->config['char_rgb_red'],
            $this->config['char_rgb_green'],
            $this->config['char_rgb_blue'],
            $this->config['char_clear']
        );

        $x = $this->xOffset;
        foreach ($this->word as $char) {
            if ($this->config['char_color_random']) {
                $ink = $this->chooseRandomColor();
            } else {
                $ink = $this->ink;
            }
            imagettftext(
                $this->image,
                $char->size,
                $char->angle,
                $x,
                $char->y,
                $ink,
                $this->fontFolder . $char->font,
                $char->element
            );
            $x += $this->config['char_space'];
        }
    }

    /**
     * @return int
     */
    private function chooseRandomColor()
    {
        do {
            $red = mt_rand(0, 255);
            $green = mt_rand(0, 255);
            $blue = mt_rand(0, 255);
        } while (!$this->isValidRandomColor($red + $green + $blue));
        return imagecolorallocatealpha($this->image, $red, $green, $blue, $this->config['char_clear']);
    }

    /**
     * @param int $color
     * @return bool
     */
    private function isValidRandomColor($color)
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

    private function paintNoise()
    {
        $nbpx = mt_rand($this->config['noise_pixel_min'], $this->config['noise_pixel_max']);
        $nbline = mt_rand($this->config['noise_line_min'], $this->config['noise_line_max']);
        $nbcircle = mt_rand($this->config['noise_circle_min'], $this->config['noise_circle_max']);
        for ($i = 0; $i < $nbpx; $i++) {
            imagesetpixel(
                $this->image,
                mt_rand(0, $this->config['crypt_width'] - 1),
                mt_rand(0, $this->config['crypt_height'] - 1),
                $this->getNoiseColor()
            );
        }
        imagesetthickness($this->image, $this->config['noise_brush_size']);
        for ($i = 0; $i < $nbline; $i++) {
            imageline(
                $this->image,
                mt_rand(0, $this->config['crypt_width'] - 1),
                mt_rand(0, $this->config['crypt_height'] - 1),
                mt_rand(0, $this->config['crypt_width'] - 1),
                mt_rand(0, $this->config['crypt_height'] - 1),
                $this->getNoiseColor()
            );
        }
        for ($i = 0; $i < $nbcircle; $i++) {
            $diameter = mt_rand(5, $this->config['crypt_width'] / 3);
            imagearc(
                $this->image,
                mt_rand(0, $this->config['crypt_width'] - 1),
                mt_rand(0, $this->config['crypt_height'] - 1),
                $diameter,
                $diameter,
                0,
                359,
                $this->getNoiseColor()
            );
        }
    }

    private function paintFrame()
    {
        $color = imagecolorallocate(
            $this->image,
            ($this->config['bg_rgb_red'] * 3 + $this->config['char_rgb_red']) / 4,
            ($this->config['bg_rgb_green'] * 3 + $this->config['char_rgb_green']) / 4,
            ($this->config['bg_rgb_blue'] * 3 + $this->config['char_rgb_blue']) / 4
        );
        imagerectangle($this->image, 0, 0, $this->config['crypt_width'] - 1, $this->config['crypt_height'] - 1, $color);
    }

    /**
     * @param string $text
     * @return resource
     */
    public function createErrorImage($text)
    {
        $text = preg_replace('/(?=\s)(.{1,15})(?:\s|$)/u', "\$1\n", $text);
        $lines = explode("\n", $text);
        $font = "{$this->fontFolder}DejaVuSans.ttf";
        $fontsize = 12;
        $padding = 5;
        $bbox = imagettfbbox($fontsize, 0, $font, $text);
        $width = $bbox[2] - $bbox[0] + 1 + 2 * $padding;
        $height = $bbox[1] - $bbox[7] + 1 + 2 * $padding;
        $bbox = imagettfbbox($fontsize, 0, $font, $lines[0]);
        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, 255, 255, 255);
        $fg = imagecolorallocate($img, 192, 0, 0);
        imagefilledrectangle($img, 0, 0, $width-1, $height-1, $bg);
        imagettftext($img, $fontsize, 0, $padding, $bbox[1]-$bbox[7]+1, $fg, $font, $text);
        return $img;
    }
}
