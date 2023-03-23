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

use Cryptographp\Infra\AudioCaptcha;
use Cryptographp\Infra\CodeGenerator;
use Cryptographp\Infra\CodeStore;
use Cryptographp\Infra\SystemChecker;
use Cryptographp\Infra\View;
use Cryptographp\Infra\VisualCaptcha;

class Dic
{
    public static function makeCaptchaController(): CaptchaController
    {
        global $pth, $plugin_cf, $plugin_tx;
        static $instance;

        if (!isset($instance)) {
            $instance = new CaptchaController(
                "{$pth['folder']['plugins']}cryptographp/",
                $plugin_tx['cryptographp'],
                self::makeCodeStore(),
                new CodeGenerator($plugin_cf['cryptographp']),
                new VisualCaptcha(
                    $pth['folder']['images'],
                    "{$pth['folder']['plugins']}cryptographp/fonts",
                    $plugin_cf['cryptographp']
                ),
                new AudioCaptcha("{$pth['folder']['plugins']}cryptographp/languages/"),
                new View("{$pth['folder']['plugins']}cryptographp/views", $plugin_tx["cryptographp"])
            );
        }
        return $instance;
    }

    public static function makeInfoController(): InfoController
    {
        global $pth, $plugin_tx;

        return new InfoController(
            "{$pth['folder']['plugins']}cryptographp/",
            $plugin_tx["cryptographp"],
            new SystemChecker()
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
}
