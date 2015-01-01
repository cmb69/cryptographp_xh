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
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net>
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
     * Whether the JavaScript has been emitted.
     *
     * @var bool
     */
    protected static $isJavaScriptEmitted;

    /**
     * Handles plugin related requests.
     *
     * @return void
     */
    public static function dispatch()
    {
        if (isset($_GET['cryptographp_mode'])) {
            switch ($_GET['cryptographp_mode']) {
            case 'video':
                self::deliverVideo();
                break;
            case 'audio':
                self::deliverAudio();
                break;
            }
        }
        if (XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
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

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('cryptographp')
            || isset($cryptographp) && $cryptographp == 'true';
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
            $o .= self::renderVersion() . self::renderSystemCheck();
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
     * @global array The localization of the plugins.
     */
    protected static function renderVersion()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['cryptographp'];
        return '<h1>Cryptographp &ndash; ' . $ptx['menu_info'] . '</h1>' . "\n"
            . tag(
                'img class="cryptographp_plugin_icon" src="'
                . $pth['folder']['plugins']
                . 'cryptographp/cryptographp.png" alt="' . $ptx['alt_logo'] . '"'
            ) . "\n"
            . '<p>Version: ' . CRYPTOGRAPHP_VERSION . '</p>'
            . "\n"
            . '<p>Copyright &copy; 2006-2007 <a href="http://www.captcha.fr/">'
            . 'Sylvain Brison</a>' . tag('br')
            . 'Copyright &copy; 2011-2015 <a href="http://3-magi.net/">'
            . 'Christoph M. Becker</a></p>' . "\n"
            . '<p class="cryptographp_license">'
            . 'This program is free software: you can redistribute it and/or modify'
            . ' it under the terms of the GNU General Public License as published by'
            . ' the Free Software Foundation, either version 3 of the License, or'
            . ' (at your option) any later version.</p>' . "\n"
            . '<p class="cryptographp_license">'
            . 'This program is distributed in the hope that it will be useful,'
            . ' but <em>without any warranty</em>; without even the implied'
            . ' warranty of <em>merchantability</em> or <em>fitness for a'
            . ' particular purpose</em>. See the'
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

        $requiredPHPVersion = '5.1.2';
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
        $o .= tag('br') . "\n";
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folder = $pth['folder']['plugins'] . 'cryptographp/' . $folder;
            $o .= (is_writable($folder) ? $ok : $warn)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
                . tag('br') . "\n";
        }
        return $o;
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
        $url = $sn . '?cryptographp_mode=video&amp;cryptographp_id='
            . $_SESSION['cryptographp_id'];
        $o = '<div class="cryptographp">' . "\n";
        $alt = XH_hsc($ptx['alt_image']);
        $o .= tag(
            'img id="cryptographp' . $_SESSION['cryptographp_id'] . '" src="'
            . $url . '" alt="' . $alt . '"'
        );
        $o .= self::emitJavaScript();
        $get = 'cryptographp_mode=audio&amp;cryptographp_id='
            . $_SESSION['cryptographp_id'] . '&amp;cryptographp_lang=' . $sl;
        $alt = XH_hsc($ptx['alt_audio']);
        $url = $sn . '?' . $get . '&amp;cryptographp_download=yes';
        $o .= '<a class="cryptographp_audio" href="' . $url . '">'
            . tag(
                'img src="' . $dir . 'images/audio.png" alt="' . $alt . '" title="'
                . $alt . '"'
            )
            . '</a>';
        $url = $sn . '?cryptographp_mode=video&amp;cryptographp_id='
            . $_SESSION['cryptographp_id'];
        $alt = XH_hsc($ptx['alt_reload']);
        $o .= '<!--<a class="cryptographp_reload" href="' . $url . '">'
            . tag(
                'img src="' . $dir . 'images/reload.png" alt="' . $alt . '" title="'
                . $alt . '"'
            )
            . '</a>-->'
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
     * Returns code to include the JavaScript.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global string The (X)HTML fragment to insert at the bottom of <body>.
     */
    protected static function emitJavaScript()
    {
        global $pth, $bjs;

        if (!self::$isJavaScriptEmitted) {
            $bjs .= '<script type="text/javascript" src="'
                . $pth['folder']['plugins'] . 'cryptographp/cryptographp.js">'
                . '</script>';
            self::$isJavaScriptEmitted = true;
        }
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
     * Delivers the visual CAPTCHA to the client.
     *
     * @return void
     */
    static function deliverVideo()
    {
        $video = new Cryptographp_VisualCAPTCHA();
        $video->render();
        exit;
    }

    /**
     * Delivers the audio CAPTCHA to the client.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    static function deliverAudio()
    {
        global $pth;

        $id = $_GET['cryptographp_id'];
        $lang = basename($_GET['cryptographp_lang']);
        if (!is_dir($pth['folder']['plugins'] . 'cryptographp/languages/' . $lang)) {
            $lang = 'en';
        }
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
