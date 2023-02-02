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

    /** @return void */
    public static function run()
    {
        if (XH_ADM) { // @phpstan-ignore-line
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('cryptographp')) {
                self::handleAdministration();
            }
        }
    }

    /** @return void */
    private static function handleAdministration()
    {
        global $admin, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= Dic::makeInfoController()();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    public static function renderCaptcha(): string
    {
        global $pth;

        $lang = basename($_GET['cryptographp_lang'] ?? "en");
        if (!is_dir("{$pth['folder']['plugins']}cryptographp/languages/$lang")) {
            $lang = 'en';
        }
        $controller = Dic::makeCaptchaController($lang);
        $action = self::getControllerAction($controller, 'cryptographp_action');
        ob_start();
        $controller->{$action}();
        $result = ob_get_clean();
        assert($result !== false);
        return $result;
    }

    private static function getControllerAction(CaptchaController $controller, string $param): string
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

    public static function checkCAPTCHA(): bool
    {
        $code = $_POST['cryptographp-captcha'] ?? "";
        if (!isset($_POST['cryptographp_nonce'])) {
            return false;
        }
        $codeStore = Dic::makeCodeStore();
        $storedCode = $codeStore->find(base64_decode($_POST['cryptographp_nonce']));
        if ($code !== $storedCode) {
            return false;
        }
        $codeStore->invalidate(base64_decode($_POST['cryptographp_nonce']));
        return true;
    }
}
