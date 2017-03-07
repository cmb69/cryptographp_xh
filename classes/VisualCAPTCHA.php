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

class VisualCAPTCHA
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var resource
     */
    protected $img;

    /**
     * @var array
     */
    protected $tword;

    /**
     * @var string
     */
    protected $word;

    /**
     * @var int
     */
    protected $ink;

    /**
     * @var int
     */
    protected $bg;

    /**
     * @var int
     */
    protected $xvariation;

    /**
     * @var int
     */
    protected $charnb;

    /**
     * @var array
     */
    protected $fonts;

    public function __construct()
    {
        global $plugin_cf;

        if (session_id() == '') {
            session_start();
        }
        $this->id = $_GET['cryptographp_id'];
        $this->fonts = explode(';', $plugin_cf['cryptographp']['char_fonts']);
    }

    public function render()
    {
        global $plugin_cf, $plugin_tx;

        $pcf = $plugin_cf['cryptographp'];

        if (!isset($_SESSION['cryptographp_id'])) {
            $this->deliverErrorImage(
                $plugin_tx['cryptographp']['error_cookies']
            );
        }

        $delay = time() - $_SESSION['cryptographp_time'][$this->id];
        if ($delay < $pcf['crypt_use_timer']) {
            if ($pcf['crypt_use_timer_error']) {
                $this->deliverErrorImage(
                    $plugin_tx['cryptographp']['error_user_time']
                );
            } else {
                sleep($pcf['crypt_use_timer'] - $delay);
            }
        }

        $this->precalculate();

        // Create the final image
        $this->img = imagecreatetruecolor($pcf['crypt_width'], $pcf['crypt_height']);
        $this->renderBackground();
        if ($pcf['noise_above']) {
            $this->renderCharacters();
            $this->renderNoise();
        } else {
            $this->renderNoise();
            $this->renderCharacters();
        }
        if ($pcf['bg_frame']) {
            $this->renderFrame();
        }
        if ($pcf['crypt_gray_scale']) {
            imagefilter($this->img, IMG_FILTER_GRAYSCALE);
        }
        if ($pcf['crypt_gaussian_blur']) {
            imagefilter($this->img, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $_SESSION['cryptographp_code'][$this->id] = $this->word;
        $_SESSION['cryptographp_time'][$this->id] = time();

        self::deliverImage();
        imagedestroy($this->img);
    }

    protected function precalculate()
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];

        // Creation du cryptogramme temporaire
        $imgtmp = imagecreatetruecolor($pcf['crypt_width'], $pcf['crypt_height']);
        $blank = imagecolorallocate($imgtmp, 255, 255, 255);
        $black = imagecolorallocate($imgtmp, 0, 0, 0);
        imagefill($imgtmp, 0, 0, $blank);

        $this->word = '';
        $x = 10;
        $pair = rand(0, 1);
        $this->charnb = rand($pcf['char_count_min'], $pcf['char_count_max']);
        for ($i = 1; $i <= $this->charnb; $i++) {
            $this->tword[$i]['font'] =  $this->fonts[array_rand($this->fonts, 1)];
            $this->tword[$i]['angle'] = rand(1, 2) == 1
                ? rand(0, $pcf['char_angle_max'])
                : rand(360 - $pcf['char_angle_max'], 360);

            if ($pcf['crypt_easy']) {
                $this->tword[$i]['element'] = !$pair
                    ? $this->getRandomCharOf($pcf['char_allowed_consonants'])
                    : $this->getRandomCharOf($pcf['char_allowed_vowels']);
            } else {
                $this->tword[$i]['element']
                    = $this->getRandomCharOf($pcf['char_allowed']);
            }

            $pair = !$pair;
            $this->tword[$i]['size'] = rand($pcf['char_size_min'], $pcf['char_size_max']);
            $lafont = $pth['folder']['plugins'] . 'cryptographp/fonts/'
                . $this->tword[$i]['font'];
            $bbox = imagettfbbox(
                $this->tword[$i]['size'],
                $this->tword[$i]['angle'],
                $lafont,
                $this->tword[$i]['element']
            );
            $min = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $max = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $delta = $pcf['crypt_height'] - $max + $min;
            $this->tword[$i]['y'] = $delta / 2 + abs($min) - 1;
            if ($pcf['char_displace']) {
                $this->tword[$i]['y'] += rand(-intval($delta / 2), intval($delta / 2));
            }
            $this->word .= $this->tword[$i]['element'];

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

            $x += $pcf['char_space'];
        }

        $width = $this->calculateTextWidth($imgtmp, $blank);
        $this->xvariation = round(($pcf['crypt_width'] - $width) / 2);
        imagedestroy($imgtmp);
    }

    /**
     * @param resource $img
     * @param int $blank
     * @return int
     */
    protected function calculateTextWidth($img, $blank)
    {
        global $plugin_cf;

        $width = $plugin_cf['cryptographp']['crypt_width'];

        $xbegin = 0;
        $x = 0;
        while ($x < $width && !$xbegin) {
            $xbegin = $this->scanColumn($img, $x, $blank);
            $x++;
        }

        $xend = 0;
        $x = $width - 1;
        while ($x > 0 && !$xend) {
            $xend = $this->scanColumn($img, $x, $blank);
            $x--;
        }
        
        return $xend - $xbegin;
    }

    /**
     * @param resource $img
     * @param int $x
     * @param int $blank
     * @return int
     */
    protected function scanColumn($img, $x, $blank)
    {
        global $plugin_cf;

        $res = 0;
        $y = 0;
        while ($y < $plugin_cf['cryptographp']['crypt_height'] && !$res) {
            if (imagecolorat($img, $x, $y) != $blank) {
                $res = $x;
            }
            $y++;
        }
        return $res;
    }
    
    /**
     * @return string
     */
    protected function getBackgroundImage()
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];
        if ($pcf['bg_image']) {
            $filename = $pth['folder']['images'] . $pcf['bg_image'];
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
     * @param string $string
     * @return string
     */
    protected function getRandomCharOf($string)
    {
        return $string[rand(0, strlen($string) - 1)];
    }

    /**
     * @return int
     */
    protected function noisecolor()
    {
        global $plugin_cf;

        switch ($plugin_cf['cryptographp']['noise_color']) {
            case 1:
                $noisecol = $this->ink;
                break;
            case 2:
                $noisecol = $this->bg;
                break;
            case 3:
            default:
                $noisecol = imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255));
        }
        return $noisecol;
    }

    /**
     * @return void
     */
    protected function renderBackground()
    {
        global $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];
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
                $this->img,
                $imgread,
                0,
                0,
                0,
                0,
                $pcf['crypt_width'],
                $pcf['crypt_height'],
                $getwidth,
                $getheight
            );
            imagedestroy($imgread);
        } else {
            $this->bg = imagecolorallocate($this->img, $pcf['bg_rgb_red'], $pcf['bg_rgb_green'], $pcf['bg_rgb_blue']);
            imagefill($this->img, 0, 0, $this->bg);
            if ($pcf['bg_clear']) {
                imagecolortransparent($this->img, $this->bg);
            }
        }
    }

    protected function renderCharacters()
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];

        $this->ink = imagecolorallocatealpha(
            $this->img,
            $pcf['char_rgb_red'],
            $pcf['char_rgb_green'],
            $pcf['char_rgb_blue'],
            $pcf['char_clear']
        );

        $x = $this->xvariation;
        for ($i = 1; $i <= $this->charnb; $i++) {
            if ($pcf['char_color_random']) {
                $rndink = $this->chooseRandomColor();
            }
            $lafont = $pth['folder']['plugins'] . 'cryptographp/fonts/'
                . $this->tword[$i]['font'];
            imagettftext(
                $this->img,
                $this->tword[$i]['size'],
                $this->tword[$i]['angle'],
                $x,
                $this->tword[$i]['y'],
                $pcf['char_color_random'] ? $rndink : $this->ink,
                $lafont,
                $this->tword[$i]['element']
            );
            $x += $pcf['char_space'];
        }
    }

    /**
     * @return int
     */
    protected function chooseRandomColor()
    {
        global $plugin_cf;

        $ok = false;
        do {
            $rndR = rand(0, 255);
            $rndG = rand(0, 255);
            $rndB = rand(0, 255);
            $rndcolor = $rndR + $rndG + $rndB;
            $ok = $this->checkRandomColor($rndcolor);
        } while (!$ok);
        return imagecolorallocatealpha($this->img, $rndR, $rndG, $rndB, $plugin_cf['cryptographp']['char_clear']);
    }

    /**
     * @param int $color
     * @return bool
     */
    protected function checkRandomColor($color)
    {
        global $plugin_cf;

        $ok = false;
        switch ($plugin_cf['cryptographp']['char_color_random_level']) {
            case 1: // very dark
                if ($color < 200) {
                    $ok = true;
                }
                break;
            case 2: // dark
                if ($color < 400) {
                    $ok = true;
                }
                break;
            case 3: // light
                if ($color > 500) {
                    $ok = true;
                }
                break;
            case 4: // very light
                if ($color > 650) {
                    $ok = true;
                }
                break;
            default:
                $ok = true;
        }
        return $ok;
    }

    protected function renderNoise()
    {
        global $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];

        $nbpx = rand($pcf['noise_pixel_min'], $pcf['noise_pixel_max']);
        $nbline = rand($pcf['noise_line_min'], $pcf['noise_line_max']);
        $nbcircle = rand($pcf['noise_circle_min'], $pcf['noise_circle_max']);
        for ($i=1; $i < $nbpx; $i++) {
            imagesetpixel(
                $this->img,
                rand(0, $pcf['crypt_width'] - 1),
                rand(0, $pcf['crypt_height'] - 1),
                $this->noisecolor()
            );
        }
        imagesetthickness($this->img, $pcf['noise_brush_size']);
        for ($i=1; $i <= $nbline; $i++) {
            imageline(
                $this->img,
                rand(0, $pcf['crypt_width'] - 1),
                rand(0, $pcf['crypt_height'] - 1),
                rand(0, $pcf['crypt_width'] - 1),
                rand(0, $pcf['crypt_height'] - 1),
                $this->noisecolor()
            );
        }
        for ($i=1; $i <= $nbcircle; $i++) {
            imagearc(
                $this->img,
                rand(0, $pcf['crypt_width'] - 1),
                rand(0, $pcf['crypt_height'] - 1),
                $rayon = rand(5, $pcf['crypt_width'] / 3),
                $rayon,
                0,
                359,
                $this->noisecolor()
            );
        }
    }

    protected function renderFrame()
    {
        global $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];
        $color = imagecolorallocate(
            $this->img,
            ($pcf['bg_rgb_red'] * 3 + $pcf['char_rgb_red']) / 4,
            ($pcf['bg_rgb_green'] * 3 + $pcf['char_rgb_green']) / 4,
            ($pcf['bg_rgb_blue'] * 3 + $pcf['char_rgb_blue']) / 4
        );
        imagerectangle($this->img, 0, 0, $pcf['crypt_width'] - 1, $pcf['crypt_height'] - 1, $color);
    }

    /**
     * @param string $text
     * @return void
     */
    protected function deliverErrorImage($text)
    {
        global $pth;

        $text = wordwrap($text, 15); // FIXME: UTF-8!
        $lines = explode("\n", $text);
        $font = $pth['folder']['plugins'] . 'cryptographp/fonts/DejaVuSans.ttf';
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
        header('Content-type: image/png');
        imagepng($img);
        exit;
    }

    protected function deliverImage()
    {
        global $plugin_cf;

        switch (strtoupper($plugin_cf['cryptographp']['crypt_format'])) {
            case 'JPG':
            case 'JPEG':
                if (imagetypes() & IMG_JPG) {
                    header('Content-type: image/jpeg');
                    imagejpeg($this->img, '', 80);
                }
                break;
            case 'GIF':
                if (imagetypes() & IMG_GIF) {
                    header('Content-type: image/gif');
                    imagegif($this->img);
                }
                break;
            default:
                if (imagetypes() & IMG_PNG) {
                    header('Content-type: image/png');
                    imagepng($this->img);
                }
        }
    }
}
