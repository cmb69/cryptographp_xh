<?php

/**
 * The plugin controller.
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
 * The plugin controller.
 *
 * @category CMSimple_XH
 * @package  Cryptographp
 * @author   Sylvain Brison <cryptographp@alphpa.com>
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */
class Cryptographp_Controller
{
    /**
     * Handles plugin related requests.
     *
     * @return void
     */
    public static function dispatch()
    {
        self::updateConfig();
        if (isset($_GET['cryptographp_mode'])) {
            if ($_GET['cryptographp_mode'] == 'audio') {
                self::deliverAudio();
            }
        }
        if (XH_ADM) {
            if (self::isAdministrationRequested()) {
                self::handleAdministration();
            }
        }
    }

    /**
     * Returns whether the plugin administration is requested.
     *
     * @return bool
     *
     * @global string Whether the plugin administration is requested.
     */
    protected static function isAdministrationRequested()
    {
        global $cryptographp;

        return isset($cryptographp) && $cryptographp == 'true';
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     * @global string The value of the <var>action</var> GP parameter.
     * @global string The (X)HTML fragment to insert at the top of the content.
     */
    protected static function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
        case '':
            $o .= self::renderVersion() . tag('hr') . self::renderSystemCheck();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'cryptographp');
        }
    }

    /**
     * Renders the plugin information.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    protected static function renderVersion()
    {
        global $pth;

        return '<h1><a href="http://3-magi.net/?CMSimple_XH/Cryptographp_XH">'
            . ' Cryptographp_XH</a></h1>' . "\n"
            . tag(
                'img class="cryptographp_plugin_icon" src="'
                . $pth['folder']['plugins']
                . 'cryptographp/cryptographp.png" alt="Plugin icon"'
            ) . "\n"
            . '<p style="margin-top: 1em">Version: ' . CRYPTOGRAPHP_VERSION . '</p>'
            . "\n"
            . '<p>Copyright &copy; 2006-2007 <a href="http://www.captcha.fr/">'
            . 'Sylvain Brison</a>' . tag('br')
            . 'Copyright &copy; 2011-2012 <a href="http://3-magi.net/">'
            . 'Christoph M. Becker</a></p>' . "\n"
            . '<p class="cryptographp_license">'
            . 'This program is free software: you can redistribute it and/or modify'
            . ' it under the terms of the GNU General Public License as published by'
            . ' the Free Software Foundation, either version 3 of the License, or'
            . ' (at your option) any later version.</p>' . "\n"
            . '<p class="cryptographp_license">'
            . 'This program is distributed in the hope that it will be useful,'
            . ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
            . ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
            . ' GNU General Public License for more details.</p>' . "\n"
            . '<p class="cryptographp_license">'
            . 'You should have received a copy of the GNU General Public License'
            . ' along with this program.  If not, see'
            . ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/'
            . '</a>.</p>' . "\n";
    }

    /**
     * Renderns the system check.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     */
    protected static function renderSystemCheck()
    {
        global $pth, $tx, $plugin_tx;

        $requiredPHPVersion = '4.3.0';
        $ptx = $plugin_tx['cryptographp'];
        $imgdir = $pth['folder']['plugins'] . 'cryptographp/images/';
        $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
        $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
        $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
        $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
            . (version_compare(PHP_VERSION, $requiredPHPVersion) >= 0 ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf(
                $ptx['syscheck_phpversion'], $requiredPHPVersion
            )
            . tag('br') . "\n";
        foreach (array('gd', 'pcre', 'session', 'spl') as $ext) {
            $o .= (extension_loaded($ext) ? $ok : $fail)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
                .tag('br') . "\n";
        }
        if (function_exists('gd_info')) {
            $gdinfo = gd_info();
            if (!isset($gdinfo['JPEG Support'])) {
                $gdinfo['JPEG Support'] = $gdinfo['JPG support'];
            }
            $support = array(
                array('FreeType Support', 'freetype'),
                array('GIF Create Support', 'gif'),
                array('JPEG Support', 'jpeg'),
                array('PNG Support', 'png')
            );
            foreach ($support as $i => $key) {
                $o .= ($gdinfo[$key[0]] ? $ok : ($i < 1 ? $fail : $warn))
                    . '&nbsp;&nbsp;' . $ptx['syscheck_' . $key[1] . '_support']
                    . tag('br') . "\n";
            }
        }
        $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
            . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes']
            . tag('br') . tag('br') . "\n";
        $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
            . '&nbsp;&nbsp;' . $ptx['syscheck_encoding']
            . tag('br') . tag('br') . "\n";
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folder = $pth['folder']['plugins'] . 'cryptographp/' . $folder;
            $o .= (is_writable($folder) ? $ok : $warn)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
                . tag('br') . "\n";
        }
        return $o;
    }

    /**
     * Updates the configuration, if necessary.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    protected static function updateConfig()
    {
        global $pth;

        $fn = $pth['folder']['plugins'].'cryptographp/config/cryptographp.cfg.php';
        $fn2 = $pth['folder']['plugins'].'cryptographp/config/config.php';
        if (!file_exists($fn) || filemtime($fn2) > filemtime($fn)) {
            if (($fh = fopen($fn, 'w')) === false
                || fwrite($fh, self::renderConfig()) === false
            ) {
                e('cntsave', 'file', $fn);
            }
            if ($fh !== false) {
                fclose($fh);
            }
        }
    }

    /**
     * Returns the Cryptographp configuration.
     *
     * @return string
     *
     * @global array The configuration of the plugins.
     */
    protected static function renderConfig()
    {
        global $plugin_cf;

        $config = array(
            'crypt_width' => array('$cryptwidth', 'int'),
            'crypt_height' => array('$cryptheight', 'int'),
            'bg_rgb_red' => array('$bgR', 'int'),
            'bg_rgb_green' => array('$bgG', 'int'),
            'bg_rgb_blue' => array('$bgB', 'int'),
            'bg_clear' => array('$bgclear', 'bool'),
            'bg_image' => array('$bgimg', 'string'),
            'bg_frame' => array('$bgframe', 'bool'),
            'char_rgb_red' => array('$charR', 'int'),
            'char_rgb_green' => array('$charG', 'int'),
            'char_rgb_blue' => array('$charB', 'int'),
            'char_color_random' => array('$charcolorrnd', 'bool'),
            'char_color_random_level' => array('$charcolorrndlevel', 'int'),
            'char_clear' => array('$charclear', 'int'),
            'fonts' => array('$tfont', 'array'),
            'char_allowed' => array('$charel', 'string'),
            'crypt_easy' => array('$crypteasy', 'bool'),
            'char_allowed_consonants' => array('$charelc', 'string'),
            'char_allowed_vowels' => array('$charelv', 'string'),
            'char_count_min' => array('$charnbmin', 'int'),
            'char_count_max' => array('$charnbmax', 'int'),
            'char_space' => array('$charspace', 'int'),
            'char_size_min' => array('$charsizemin', 'int'),
            'char_size_max' => array('$charsizemax', 'int'),
            'char_angle_max' => array('$charanglemax', 'int'),
            'char_displace' => array('$charup', 'bool'),
            'crypt_gaussian_blur' => array('$cryptgaussianblur', 'bool'),
            'crypt_gray_scale' => array('$cryptgrayscal', 'bool'),
            'noise_pixel_min' => array('$noisepxmin', 'int'),
            'noise_pixel_max' => array('$noisepxmax', 'int'),
            'noise_line_min' => array('$noiselinemin', 'int'),
            'noise_line_max' => array('$noiselinemax', 'int'),
            'noise_circle_min' => array('$nbcirclemin', 'int'),
            'noise_circle_max' => array('$nbcirclemax', 'int'),
            'noise_color_char' => array('$noisecolorchar', 'int'),
            'noise_brush_size' => array('$brushsize', 'int'),
            'noise_above' => array('$noiseup', 'bool'),
            'crypt_format' => array('$cryptformat', 'string'),
            'crypt_use_timer' => array('$cryptusetimer', 'int'),
            'crypt_use_timer_error' => array('$cryptusertimererror', 'bool'),
            'crypt_expiration' => array('$cryptexpiration', 'int')
        );

        $pcf = $plugin_cf['cryptographp'];
        $res = '<?php' . "\n\n"
            . '// This file was automatically generated by Cryptographp_XH.'
            . "\n\n";
        foreach ($config as $key => $option) {
            list($varname, $type) = $option;
            switch ($type) {
            case 'int':
                $val = $pcf[$key];
                break;
            case 'bool':
                $val = strtolower($pcf[$key] == 'yes') ? 'TRUE' : 'FALSE';
                break;
            case 'string':
                $val = '\'' . addcslashes($pcf[$key], '\'\\') . '\'';
                break;
            case 'array':
                $val = 'array(\'' . implode('\', \'', explode(';', $pcf[$key]))
                    . '\')';
                break;
            }
            $res .= $varname . ' = ' . $val . ';' . "\n";
        }
        $res .= "\n" . '?>' . "\n";
        return $res;
    }

    /**
     * Returns the (x)html block element displaying the captcha,
     * the input field for the captcha code and all other elements,
     * that are related directly to the captcha,
     * such as an reload and an audio button.
     *
     * @return string
     *
     * @global array  The paths of system files and folders.
     * @global string The current language.
     * @global array  The localization of the plugins.
     */
    public static function renderCAPTCHA()
    {
        global $pth, $sn, $sl, $plugin_tx;

        if (session_id() == '') {
            session_start();
        }
        $_SESSION['cryptographp_id'] = isset($_SESSION['cryptographp_id'])
            ? $_SESSION['cryptographp_id'] + 1 : 1;
        $dir = $pth['folder']['plugins'] . 'cryptographp/';
        $ptx = $plugin_tx['cryptographp'];
        $get = 'id=' . $_SESSION['cryptographp_id'].'&amp;lang=' . $sl;
        $o = '<div class="cryptographp">' . "\n";
        $alt = htmlspecialchars($ptx['alt_image'], ENT_QUOTES);
        $o .= tag(
            'img id="cryptographp' . $_SESSION['cryptographp_id'] . '" src="'
            . $dir . 'cryptographp.php?' . $get . '" alt="' . $alt . '"'
        ) . "\n";
        $o .= self::emitPlayer();
        $get = 'cryptographp_mode=audio&amp;cryptographp_id='
            . $_SESSION['cryptographp_id'] . '&amp;cryptographp_lang=' . $sl;
        $alt = htmlspecialchars($ptx['alt_audio'], ENT_QUOTES);
        $url = $sn . '?' . $get . '&amp;cryptographp_download=yes';
        $o .= '<a href="' . $url . '" onclick="Cryptographp.play('
            . $_SESSION['cryptographp_id'].'); return false">'
            . tag(
                'img src="' . $dir . 'images/audio.png" alt="' . $alt . '" title="'
                . $alt . '"'
            )
            . '</a>' . "\n";
        $alt = htmlspecialchars($ptx['alt_reload'], ENT_QUOTES);
        $o .= '<a class="cryptographp_reload" style="display: none"'
            . ' href="javascript:Cryptographp.reload('
            . $_SESSION['cryptographp_id'] . ')">'
            . tag(
                'img src="' . $dir . 'images/reload.png" alt="' . $alt . '" title="'
                . $alt . '"'
            )
            . '</a>' . "\n"
            . '<div>' . $ptx['message_enter_code'] . '</div>' . "\n"
            . tag('input type="text" name="cryptographp-captcha"')
            . tag(
                'input type="hidden" name="cryptographp_id" value="'
                .$_SESSION['cryptographp_id'] . '"'
            )
            . '</div>' . "\n";
        return $o;
    }

    /**
     * Returns code to include the jplayer.
     *
     * @return string (X)HTML.
     */
    protected static function emitPlayer()
    {
        global $hjs, $pth, $sn, $sl, $plugin_cf;
        static $again = false;

        if (!$again) {
            $again = true;
            $dir = $pth['folder']['plugins'].'cryptographp/';
            include_once $pth['folder']['plugins'].'jquery/jquery.inc.php';
            include_jquery();
            include_jqueryplugin('jplayer', $dir.'jquery.jplayer.min.js');
            $hjs .= '<script type="text/javascript" src="' . $dir
                . 'cryptographp.js"></script>' . "\n"
                . '<script type="text/javascript">/* <![CDATA[ */' . "\n"
                . 'Cryptographp.URL = "' . $sn . '";' . "\n"
                . 'Cryptographp.DIR = \'' . $dir . '\';' . "\n"
                . 'Cryptographp.LANG = \'' . $sl . '\';' . "\n"
                . '/* ]]> */</script>' . "\n";
        }
        $o = '<span id="cryptographp_player' . $_SESSION['cryptographp_id']
            . '" class="cryptographp_player"></span>';
        return $o;
    }

    /**
     * Returns whether the correct captcha code was entered.
     *
     * @return bool
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    public static function checkCAPTCHA()
    {
        global $pth, $plugin_cf;

        if (session_id() == '') {
            session_start();
        }
        $id = stsl($_POST['cryptographp_id']);
        $code = stsl($_POST['cryptographp-captcha']);
        $ok = isset($_SESSION['cryptographp_code'][$id])
            && $_SESSION['cryptographp_code'][$id] == $code
            && $_SESSION['cryptographp_time'][$id]
            + $plugin_cf['cryptographp']['crypt_expiration'] >= time();
        unset(
            $_SESSION['cryptographp_code'][$id], $_SESSION['cryptographp_lang'][$id],
            $_SESSION['cryptographp_time'][$id]
        );
        return $ok;
    }

    /**
     * Delivers the audio CAPTCHA to the client.
     *
     * @return void
     */
    static function deliverAudio()
    {
        $id = $_GET['cryptographp_id'];
        $lang = basename($_GET['cryptographp_lang']);
        if (session_id() == '') {
            session_start();
        }
        if (!isset($_SESSION['cryptographp_code'][$id])) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        $o = Cryptographp_Controller::makeAudio($id, $lang);
        header('Content-Type: audio/mpeg');
        if (isset($_GET['cryptographp_download'])) {
            header('Content-Disposition: attachment; filename="captcha.mp3"');
        }
        header('Content-Length: ' . strlen($o));
        echo $o;
    }

    /**
     * Creates and returns an audio CAPTCHA.
     *
     * @param string $id   A CAPTCHA ID.
     * @param string $lang A language code.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    protected static function makeAudio($id, $lang)
    {
        global $pth, $plugin_tx;

        $code = $_SESSION['cryptographp_code'][$id];
        $o = '';
        for ($i = 0; $i < strlen($code); $i++) {
            $cnt = file_get_contents(
                $pth['folder']['plugins'] . 'cryptographp/languages/'
                . $lang . '/' . strtolower($code[$i]) . '.mp3'
            );
            if ($cnt !== false) {
                $o .= $cnt;
            } else {
                exit($plugin_tx['cryptographp']['error_audio']);
            }
        }
        return $o;
    }

}

?>
