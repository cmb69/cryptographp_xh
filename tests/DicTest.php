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

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;
        $pth = ["folder" => ["content" => "", "images" => "", "plugins" => ""]];
        $plugin_cf = ["cryptographp" => ["char_fonts" => "", "crypt_expiration" => ""]];
        $plugin_tx = ["cryptographp" => []];
    }

    public function testMakesCaptchaController(): void
    {
        $this->assertInstanceOf(CaptchaController::class, Dic::makeCaptchaController());
    }

    public function testMakesInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::makeInfoController());
    }
}
