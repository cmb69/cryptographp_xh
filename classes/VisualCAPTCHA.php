<?php

/**
 * The visual CAPTCHAs.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Cryptographp
 * @author    Sylvain Brison <cryptographp@alphpa.com>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */

/**
 * The visual CAPTCHAs.
 *
 * @category CMSimple_XH
 * @package  Cryptographp
 * @author   Sylvain Brison <cryptographp@alphpa.com>
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */
class Cryptographp_VisualCAPTCHA
{
    /**
     * The ID of this CAPTCHA.
     *
     * @var int
     */
    protected $id;

    /**
     * The GD image.
     *
     * @var resource
     */
    protected $img;

    /**
     * The array of records for each character.
     *
     * @var array
     */
    protected $tword;

    /**
     * The (current?) color.
     *
     * @var int
     */
    protected $ink;

    /**
     * The background color.
     *
     * @var int
     */
    protected $bg;

    /**
     * The horizontal start position of the characters.
     *
     * @var int
     */
    protected $xvariation;

    /**
     * The number of characters.
     *
     * @var int
     */
    protected $charnb;

    /**
     * The array of fonts.
     *
     * @var array
     */
    protected $fonts;

    /**
     * Initializes a new instance.
     *
     * @global array The configuration of the plugins.
     */
    public function __construct()
    {
        global $plugin_cf;

        if (session_id() == '') {
            session_start();
        }
        $this->id = $_GET['cryptographp_id'];
        $this->fonts = explode(';', $plugin_cf['cryptographp']['char_fonts']);
    }

    /**
     * Renders the CAPTCHA.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     * @global array The localization of the plugins.
     */
    public function render()
    {
        global $pth, $plugin_cf, $plugin_tx;

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

        // Creation du cryptogramme temporaire
        $imgtmp = imagecreatetruecolor($pcf['crypt_width'], $pcf['crypt_height']);
        $blank = imagecolorallocate($imgtmp, 255, 255, 255);
        $black = imagecolorallocate($imgtmp, 0, 0, 0);
        imagefill($imgtmp, 0, 0, $blank);

        $word = '';
        $x = 10;
        $pair = rand(0, 1);
        $this->charnb = rand($pcf['char_count_min'], $pcf['char_count_max']);
        for ($i = 1; $i <= $this->charnb; $i++) {
            $this->tword[$i]['font'] =  $this->fonts[array_rand($this->fonts, 1)];
            $this->tword[$i]['angle'] = rand(1, 2) == 1
                ? rand(0, $pcf['char_angle_max'])
                : rand(360 - $pcf['char_angle_max'], 360);

            if ($plugin_cf['cryptographp']['crypt_easy']) {
                $this->tword[$i]['element'] = !$pair
                    ? $this->getRandomCharOf($pcf['char_allowed_consonants'])
                    : $this->getRandomCharOf($pcf['char_allowed_vowels']);
            } else {
                $this->tword[$i]['element']
                    = $this->getRandomCharOf($pcf['char_allowed']);
            }

            $pair = !$pair;
            $this->tword[$i]['size'] = rand(
                $pcf['char_size_min'], $pcf['char_size_max']
            );
            $lafont = $pth['folder']['plugins'] . 'cryptographp/fonts/'
                . $this->tword[$i]['font'];
            $bbox = imagettfbbox(
                $this->tword[$i]['size'], $this->tword[$i]['angle'],
                $lafont, $this->tword[$i]['element']
            );
            $min = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $max = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
            $delta = $pcf['crypt_height'] - $max + $min;
            $this->tword[$i]['y'] = $delta / 2 + abs($min) - 1;
            if ($pcf['char_displace']) {
                $this->tword[$i]['y'] += rand(
                    -intval($delta / 2), intval($delta / 2)
                );
            }
            $word .= $this->tword[$i]['element'];

            imagettftext(
                $imgtmp, $this->tword[$i]['size'], $this->tword[$i]['angle'],
                $x, $this->tword[$i]['y'],
                $black, $lafont, $this->tword[$i]['element']
            );

            $x += $pcf['char_space'];
        }

        // Calcul du racadrage horizontal du cryptogramme temporaire
        $xbegin = 0;
        $x = 0;
        while ($x < $pcf['crypt_width'] && !$xbegin) {
            $y = 0;
            while ($y < $pcf['crypt_height'] && !$xbegin) {
                if (imagecolorat($imgtmp, $x, $y) != $blank) {
                    $xbegin = $x;
                }
                $y++;
            }
            $x++;
        }

        $xend = 0;
        $x = $pcf['crypt_width'] - 1;
        while ($x > 0 && !$xend) {
            $y = 0;
            while ($y < $pcf['crypt_height'] && !$xend) {
                if (imagecolorat($imgtmp, $x, $y) != $blank) {
                    $xend = $x;
                }
                $y++;
            }
            $x--;
        }

        $this->xvariation = round($pcf['crypt_width'] / 2 - ($xend - $xbegin) / 2);
        imagedestroy($imgtmp);

        // Creation du cryptogramme definitif
        // Creation du fond
        $this->img = imagecreatetruecolor($pcf['crypt_width'], $pcf['crypt_height']);

        $bgimg = $this->getBackgroundImage();
        if ($bgimg) {
            list($getwidth, $getheight, $gettype, $getattr) = getimagesize($bgimg);
            switch ($gettype) {
            case "1":
                $imgread = imagecreatefromgif($bgimg);
                break;
            case "2":
                $imgread = imagecreatefromjpeg($bgimg);
                break;
            case "3":
                $imgread = imagecreatefrompng($bgimg);
                break;
            }
            imagecopyresized(
                $this->img, $imgread, 0, 0, 0, 0,
                $pcf['crypt_width'], $pcf['crypt_height'], $getwidth, $getheight
            );
            imagedestroy($imgread);
        } else {
            $this->bg = imagecolorallocate(
                $img, $pcf['bg_rgb_red'], $pcf['bg_rgb_green'], $pcf['bg_rgb_blue']
            );
            imagefill($this->img, 0, 0, $this->bg);
            if ($pcf['bg_clear']) {
                imagecolortransparent($this->img, $this->bg);
            }
        }

        if ($pcf['noise_above']) {
            $this->ecriture();
            $this->bruit();
        } else {
            $this->bruit();
            $this->ecriture();
        }

        /*
         * Create the frame.
         */
        if ($pcf['bg_frame']) {
            $framecol = imagecolorallocate(
                $this->img,
                ($pcf['bg_rgb_red'] * 3 + $pcf['char_rgb_red']) / 4,
                ($pcf['bg_rgb_green'] * 3 + $pcf['char_rgb_green']) / 4,
                ($pcf['bg_rgb_blue'] * 3 + $pcf['char_rgb_blue']) / 4
            );
            imagerectangle(
                $this->img, 0, 0, $pcf['crypt_width'] - 1, $pcf['crypt_height'] - 1,
                $framecol
            );
        }

        /*
         * Additional transformations: Grayscale and Interference
         */
        if ($pcf['crypt_gray_scale']) {
            imagefilter($this->img, IMG_FILTER_GRAYSCALE);
        }
        if ($pcf['crypt_gaussian_blur']) {
            imagefilter($this->img, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $_SESSION['cryptographp_code'][$this->id] = $word;
        $_SESSION['cryptographp_time'][$this->id] = time();

        /*
         * Send the final image to the browser.
         */
        switch (strtoupper($pcf['crypt_format'])) {
        case "JPG":
        case "JPEG":
            if (imagetypes() & IMG_JPG) {
                header("Content-type: image/jpeg");
                imagejpeg($this->img, '', 80);
            }
            break;
        case "GIF":
            if (imagetypes() & IMG_GIF) {
                header("Content-type: image/gif");
                imagegif($this->img);
            }
            break;
        default:
            if (imagetypes() & IMG_PNG) {
                header("Content-type: image/png");
                imagepng($this->img);
            }
        }
        imagedestroy($this->img);
    }

    /**
     * Returns the background image path.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
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
     * Returns a random character of a string.
     *
     * @param string $string A string.
     *
     * @return string
     */
    protected function getRandomCharOf($string)
    {
        return $string[rand(0, strlen($string) - 1)];
    }

    /**
     * Returns the color of the noise and the brush shape.
     *
     * @return int
     *
     * @global array The configuration of the plugins.
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
            $noisecol = imagecolorallocate(
                $this->img, rand(0, 255), rand(0, 255), rand(0, 255)
            );
        }
        return $noisecol;
    }

    /**
     * Renders the characters.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    protected function ecriture()
    {
        global $pth, $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];

        $this->ink = imagecolorallocatealpha(
            $this->img, $pcf['char_rgb_red'], $pcf['char_rgb_green'],
            $pcf['char_rgb_blue'], $pcf['char_clear']
        );

        $x = $this->xvariation;
        for ($i = 1; $i <= $this->charnb; $i++) {

            if ($pcf['char_color_random']) {   // Choisit des couleurs au hasard
                $ok = false;
                do {
                    $rndR = rand(0, 255); $rndG = rand(0, 255); $rndB = rand(0, 255);
                    $rndcolor = $rndR + $rndG + $rndB;
                    switch ($pcf['char_color_random_level']) {
                    case 1: // very dark
                        if ($rndcolor < 200) {
                            $ok = true;
                        }
                        break;
                    case 2: // dark
                        if ($rndcolor < 400) {
                            $ok = true;
                        }
                        break;
                    case 3: // light
                        if ($rndcolor > 500) {
                            $ok = true;
                        }
                        break;
                    case 4: // very light
                        if ($rndcolor > 650) {
                            $ok = true;
                        }
                        break;
                    default:
                        $ok = true;
                    }
                } while (!$ok);

                $rndink = imagecolorallocatealpha(
                    $this->img, $rndR, $rndG, $rndB, $pcf['char_clear']
                );
            }

            $lafont = $pth['folder']['plugins'] . 'cryptographp/fonts/'
                . $this->tword[$i]['font'];
            imagettftext(
                $this->img, $this->tword[$i]['size'], $this->tword[$i]['angle'],
                $x, $this->tword[$i]['y'],
                $pcf['char_color_random'] ? $rndink : $this->ink,
                $lafont, $this->tword[$i]['element']
            );

            $x += $pcf['char_space'];
        }
    }

    /**
     * Adds noise: point, random lines and circles.
     *
     * @return void
     *
     * @global array The configuration of the plugins.
     */
    protected function bruit()
    {
        global $plugin_cf;

        $pcf = $plugin_cf['cryptographp'];

        $nbpx = rand($pcf['noise_pixel_min'], $pcf['noise_pixel_max']);
        $nbline = rand($pcf['noise_line_min'], $pcf['noise_line_max']);
        $nbcircle = rand($pcf['noise_circle_min'], $pcf['noise_circle_max']);
        for ($i=1; $i < $nbpx; $i++) {
            imagesetpixel(
                $this->img, rand(0, $pcf['crypt_width'] - 1),
                rand(0, $pcf['crypt_height'] - 1), $this->noisecolor()
            );
        }
        imagesetthickness($this->img, $pcf['noise_brush_size']);
        for ($i=1; $i <= $nbline; $i++) {
            imageline(
                $this->img,
                rand(0, $pcf['crypt_width'] - 1), rand(0, $pcf['crypt_height'] - 1),
                rand(0, $pcf['crypt_width'] - 1), rand(0, $pcf['crypt_height'] - 1),
                $this->noisecolor()
            );
        }
        for ($i=1; $i <= $nbcircle; $i++) {
            imagearc(
                $this->img,
                rand(0, $pcf['crypt_width'] - 1), rand(0, $pcf['crypt_height'] - 1),
                $rayon = rand(5, $pcf['crypt_width'] / 3), $rayon, 0, 359,
                $this->noisecolor()
            );
        }
    }

    /**
     * Delivers an image with an error message text.
     *
     * @param string $text An error message.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
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
        imagettftext(
            $img, $fontsize, 0, $padding, $bbox[1]-$bbox[7]+1, $fg, $font, $text
        );
        header('Content-type: image/png');
        imagepng($img);
        exit;
    }
}

?>
