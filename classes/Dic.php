<?php

/**
 * Copyright 2023 Christoph M. Becker
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

use Cryptographp\Model\AudioCaptcha;
use Cryptographp\Model\CodeGenerator;
use Cryptographp\Model\CodeStore;
use Cryptographp\Model\VisualCaptcha;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function makeCaptchaController(): CaptchaController
    {
        global $pth, $plugin_cf;
        static $instance;

        if (!isset($instance)) {
            $instance = new CaptchaController(
                "{$pth['folder']['plugins']}cryptographp/",
                self::makeCodeStore(),
                new CodeGenerator($plugin_cf['cryptographp']),
                new VisualCaptcha(
                    $pth['folder']['images'],
                    "{$pth['folder']['plugins']}cryptographp/fonts",
                    $plugin_cf['cryptographp']
                ),
                new AudioCaptcha("{$pth['folder']['plugins']}cryptographp/languages/"),
                self::makeView()
            );
        }
        return $instance;
    }

    public static function makeInfoController(): InfoController
    {
        global $pth;
        return new InfoController(
            "{$pth['folder']['plugins']}cryptographp/",
            new SystemChecker(),
            self::makeView()
        );
    }

    private static function makeCodeStore(): CodeStore
    {
        global $pth, $plugin_cf;

        return new CodeStore(
            "{$pth['folder']['content']}cryptographp.dat",
            time(),
            (int) $plugin_cf['cryptographp']['crypt_expiration']
        );
    }

    private static function makeView(): View
    {
        global $pth, $plugin_tx;
        return new View("{$pth['folder']['plugins']}cryptographp/views/", $plugin_tx["cryptographp"]);
    }
}
