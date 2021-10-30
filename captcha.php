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

use Cryptographp\AudioCaptcha;
use Cryptographp\CodeGenerator;
use Cryptographp\View;
use Cryptographp\VisualCaptcha;

/**
 * @return string
 */
function cryptographp_captcha_display()
{
    global $pth, $sl, $plugin_cf, $plugin_tx;

    $lang = basename($_GET['cryptographp_lang'] ?? "en");
    if (!is_dir("{$pth['folder']['plugins']}cryptographp/languages/$lang")) {
        $lang = 'en';
    }
    $controller = new Cryptographp\CaptchaController(
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
    $action = Cryptographp\Plugin::getControllerAction($controller, 'cryptographp_action');
    ob_start();
    $controller->{$action}();
    return ob_get_clean();
}

/**
 * @return bool
 */
function cryptographp_captcha_check()
{
    return Cryptographp\Plugin::checkCAPTCHA();
}
