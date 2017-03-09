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

class VisualCaptcha
{
    /**
     * @var resource
     */
    private $image;

    /**
     * @var array
     */
    private $tword;

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
    private $xvariation;

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
        $this->fontFolder = "{$pth['folder']['plugins']}cryptographp/fonts/";
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
        $imgtmp = imagecreatetruecolor($this->config['crypt_width'], $this->config['crypt_height']);
        $blank = imagecolorallocate($imgtmp, 255, 255, 255);
        $black = imagecolorallocate($imgtmp, 0, 0, 0);
        imagefill($imgtmp, 0, 0, $blank);

        $x = 10;
        for ($i = 1; $i <= strlen($this->code); $i++) {
            $this->tword[$i]['font'] =  $this->fonts[array_rand($this->fonts, 1)];
            $this->tword[$i]['angle'] = rand(1, 2) == 1
                ? rand(0, $this->config['char_angle_max'])
                : rand(360 - $this->config['char_angle_max'], 360);

            $this->tword[$i]['element'] = $this->code[$i-1];

            $this->tword[$i]['size'] = rand($this->config['char_size_min'], $this->config['char_size_max']);
            $lafont = $this->fontFolder . $this->tword[$i]['font'];
            $bbox = imagettfbbox(
                $this->tword[$i]['size'],
                $this->tword[$i]['angle'],
                $lafont,
                $this->tword[$i]['element']
            );
            $min = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $max = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $delta = $this->config['crypt_height'] - $max + $min;
            $this->tword[$i]['y'] = $delta / 2 + abs($min) - 1;
            if ($this->config['char_displace']) {
                $this->tword[$i]['y'] += rand(-intval($delta / 2), intval($delta / 2));
            }

            imagettftext(
                $imgtmp,
                $this->tword[$i]['size'],
                $this->tword[$i]['angle'],
                $x,
                $this->tword[$i]['y'],
                $black,
                $lafont,
                $this->tword[$i]['element']
            );

            $x += $this->config['char_space'];
        }

        $width = $this->calculateTextWidth($imgtmp, $blank);
        $this->xvariation = round(($this->config['crypt_width'] - $width) / 2);
        imagedestroy($imgtmp);
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
        $x = 0;
        while ($x < $width && !$xbegin) {
            $xbegin = $this->scanColumn($image, $x, $blank);
            $x++;
        }

        $xend = 0;
        $x = $width - 1;
        while ($x > 0 && !$xend) {
            $xend = $this->scanColumn($image, $x, $blank);
            $x--;
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
    private function getBackgroundImage()
    {
        if ($this->config['bg_image']) {
            $filename = $this->imageFolder . $this->config['bg_image'];
            if (is_dir($filename)) {
                if ($dh  = opendir($filename)) {
                    while (($entry = readdir($dh)) != false) {
                        if (preg_match('/\.(gif|jpg|png)$/', $entry)) {
                            $files[] = $entry;
                        }
                    }
                    closedir($dh);
                }
                return $filename . '/' . $files[array_rand($files, 1)];
            } elseif (is_file($filename)) {
                return $filename;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    private function noisecolor()
    {
        switch ($this->config['noise_color']) {
            case 1:
                $noisecol = $this->ink;
                break;
            case 2:
                $noisecol = $this->bg;
                break;
            case 3:
            default:
                $noisecol = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
        }
        return $noisecol;
    }

    /**
     * @return void
     */
    private function paintBackground()
    {
        $bgimg = $this->getBackgroundImage();
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

        $x = $this->xvariation;
        for ($i = 1; $i <= strlen($this->code); $i++) {
            if ($this->config['char_color_random']) {
                $rndink = $this->chooseRandomColor();
            }
            $lafont = $this->fontFolder . $this->tword[$i]['font'];
            imagettftext(
                $this->image,
                $this->tword[$i]['size'],
                $this->tword[$i]['angle'],
                $x,
                $this->tword[$i]['y'],
                $this->config['char_color_random'] ? $rndink : $this->ink,
                $lafont,
                $this->tword[$i]['element']
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
            $red = rand(0, 255);
            $green = rand(0, 255);
            $blue = rand(0, 255);
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
                if ($color < 200) {
                    return true;
                }
                return false;
            case 2: // dark
                if ($color < 400) {
                    return true;
                }
                return false;
            case 3: // light
                if ($color > 500) {
                    return true;
                }
                return false;
            case 4: // very light
                if ($color > 650) {
                    return true;
                }
                return false;
            default:
                return true;
        }
    }

    private function paintNoise()
    {
        $nbpx = rand($this->config['noise_pixel_min'], $this->config['noise_pixel_max']);
        $nbline = rand($this->config['noise_line_min'], $this->config['noise_line_max']);
        $nbcircle = rand($this->config['noise_circle_min'], $this->config['noise_circle_max']);
        for ($i=1; $i < $nbpx; $i++) {
            imagesetpixel(
                $this->image,
                rand(0, $this->config['crypt_width'] - 1),
                rand(0, $this->config['crypt_height'] - 1),
                $this->noisecolor()
            );
        }
        imagesetthickness($this->image, $this->config['noise_brush_size']);
        for ($i=1; $i <= $nbline; $i++) {
            imageline(
                $this->image,
                rand(0, $this->config['crypt_width'] - 1),
                rand(0, $this->config['crypt_height'] - 1),
                rand(0, $this->config['crypt_width'] - 1),
                rand(0, $this->config['crypt_height'] - 1),
                $this->noisecolor()
            );
        }
        for ($i=1; $i <= $nbcircle; $i++) {
            imagearc(
                $this->image,
                rand(0, $this->config['crypt_width'] - 1),
                rand(0, $this->config['crypt_height'] - 1),
                $rayon = rand(5, $this->config['crypt_width'] / 3),
                $rayon,
                0,
                359,
                $this->noisecolor()
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
        $text = wordwrap($text, 15); // FIXME: UTF-8!
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
