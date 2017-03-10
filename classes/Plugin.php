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

    /**
     * @param string $param
     * @return string
     */
    public static function getControllerAction(CaptchaController $controller, $param)
    {
        $action = preg_replace_callback(
            '/_([a-z])/',
            function ($matches) {
                return ucfirst($matches[1]);
            },
            isset($_GET[$param]) ? stsl($_GET[$param]) : 'default'
        );
        if (!method_exists($controller, "{$action}Action")) {
            $action = 'default';
        }
        return "{$action}Action";
    }

    public static function dispatch()
    {
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
                $o .= self::renderInfo();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'cryptographp');
        }
    }

    /**
     * @return string
     */
    protected static function renderInfo()
    {
        global $pth;

        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}cryptographp/cryptographp.png";
        $view->version = CRYPTOGRAPHP_VERSION;
        $view->checks = (new SystemCheckService)->getChecks();
        return (string) $view;
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
