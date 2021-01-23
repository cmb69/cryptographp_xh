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
    const VERSION = '1.0beta6';

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
            isset($_GET[$param]) ? $_GET[$param] : 'default'
        );
        if (!method_exists($controller, "{$action}Action")) {
            $action = 'default';
        }
        return "{$action}Action";
    }

    public function run()
    {
        if (XH_ADM) {
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('cryptographp')) {
                $this->handleAdministration();
            }
        }
    }

    private function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= $this->renderInfo();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'cryptographp');
        }
    }

    /**
     * @return string
     */
    private function renderInfo()
    {
        global $pth;

        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}cryptographp/cryptographp.png";
        $view->version = self::VERSION;
        $view->checks = (new SystemCheckService)->getChecks();
        return (string) $view;
    }

    /**
     * @return bool
     */
    public static function checkCAPTCHA()
    {
        global $plugin_cf;

        if (function_exists('XH_startSession')) {
            XH_startSession();
        } elseif (session_id() == '') {
            session_start();
        }
        $code = $_POST['cryptographp-captcha'];
        $unexpired = isset($_SESSION['cryptographp_time'])
            && $_SESSION['cryptographp_time'] + $plugin_cf['cryptographp']['crypt_expiration'] >= time();
        $ok = isset($_SESSION['cryptographp_code'])
            && $_SESSION['cryptographp_code'] == $code
            && $unexpired;
        if ($ok || !$unexpired) {
            unset($_SESSION['cryptographp_code'], $_SESSION['cryptographp_time']);
        }
        return $ok;
    }
}
