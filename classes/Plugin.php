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

namespace Cryptographp;

class Plugin
{
    const VERSION = '1.0beta6';

    /**
     * @return void
     */
    public static function run()
    {
        if (XH_ADM) { // @phpstan-ignore-line
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('cryptographp')) {
                self::handleAdministration();
            }
        }
    }

    /**
     * @return void
     */
    private static function handleAdministration()
    {
        global $admin, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= self::renderInfo();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    /**
     * @return string
     */
    private static function renderInfo()
    {
        global $pth, $plugin_tx;

        $view = new View("{$pth['folder']['plugins']}cryptographp/views", $plugin_tx["cryptographp"]);
        $systemCheckService = new SystemCheckService(
            "{$pth['folder']['plugins']}cryptographp",
            $plugin_tx["cryptographp"]
        );
        return $view->render('info', [
            'version' => self::VERSION,
            'checks' => $systemCheckService->getChecks(),
        ]);
    }

    public static function renderCaptcha(): string
    {
        global $pth, $sl, $plugin_cf, $plugin_tx;

        $lang = basename($_GET['cryptographp_lang'] ?? "en");
        if (!is_dir("{$pth['folder']['plugins']}cryptographp/languages/$lang")) {
            $lang = 'en';
        }
        $controller = new CaptchaController(
            "{$pth['folder']['plugins']}cryptographp/",
            $sl,
            $plugin_cf['cryptographp'],
            $plugin_tx['cryptographp'],
            new CodeGenerator($plugin_cf['cryptographp']),
            new VisualCaptcha(
                $pth['folder']['images'],
                "{$pth['folder']['plugins']}cryptographp/fonts",
                $plugin_cf['cryptographp']
            ),
            new AudioCaptcha("{$pth['folder']['plugins']}cryptographp/languages/$lang/"),
            new View("{$pth['folder']['plugins']}cryptographp/views", $plugin_tx["cryptographp"])
        );
        $action = self::getControllerAction($controller, 'cryptographp_action');
        ob_start();
        $controller->{$action}();
        return ob_get_clean();
    }

    /**
     * @param string $param
     * @return string
     */
    private static function getControllerAction(CaptchaController $controller, $param)
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

    /**
     * @return bool
     */
    public static function checkCAPTCHA()
    {
        global $plugin_cf;

        XH_startSession();
        $code = $_POST['cryptographp-captcha'];
        $unexpired = isset($_SESSION['cryptographp_time'])
            && $_SESSION['cryptographp_time'] + (int) $plugin_cf['cryptographp']['crypt_expiration'] >= time();
        $ok = isset($_SESSION['cryptographp_code'])
            && $_SESSION['cryptographp_code'] == $code
            && $unexpired;
        if ($ok || !$unexpired) {
            unset($_SESSION['cryptographp_code'], $_SESSION['cryptographp_time']);
        }
        return $ok;
    }
}
