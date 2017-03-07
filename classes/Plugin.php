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

class Plugin
{
    /**
     * @var bool
     */
    protected static $isJavaScriptEmitted;

    public static function dispatch()
    {
        if (isset($_GET['cryptographp_mode'])) {
            switch ($_GET['cryptographp_mode']) {
                case 'video':
                    $video = new VisualCaptcha();
                    $video->render();
                    exit;
                case 'audio':
                    $captcha = new AudioCaptcha();
                    $captcha->deliver();
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
     * @return bool
     */
    protected static function isAdministrationRequested()
    {
        global $cryptographp;

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('cryptographp')
            || isset($cryptographp) && $cryptographp == 'true';
    }

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
     * @return string
     */
    protected static function renderVersion()
    {
        global $pth;

        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}cryptographp/cryptographp.png";
        $view->version = CRYPTOGRAPHP_VERSION;
        return (string) $view;
    }

    /**
     * @return string
     */
    protected static function renderSystemCheck()
    {
        $systemCheck = new SystemCheck();
        return $systemCheck->render();
    }

    /**
     * @return string
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
     * @return string
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
     * @return bool
     */
    public static function checkCAPTCHA()
    {
        global $plugin_cf;

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
            $_SESSION['cryptographp_code'][$id],
            $_SESSION['cryptographp_lang'][$id],
            $_SESSION['cryptographp_time'][$id]
        );
        return $ok;
    }
}
