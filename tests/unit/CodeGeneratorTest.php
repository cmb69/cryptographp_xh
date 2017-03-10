<?php

/**
 * Copyright 2017 Christoph M. Becker
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

use PHPUnit_Framework_TestCase;

class CodeGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CodeGenerator
     */
    private $subject;

    public function setUp()
    {
        $this->setUpConfig();
        $this->subject = new CodeGenerator;
    }

    private function setUpConfig()
    {
        global $plugin_cf;

        $plugin_cf['cryptographp'] = array(
            'char_allowed' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
            'char_allowed_consonants' => 'BCDFGHJKLMNPQRSTVWXZ',
            'char_allowed_vowels' => 'AEIOUY',
            'char_count_max' => '4',
            'char_count_min' => '4',
            'crypt_easy' => 'true'
        );
    }

    public function testCreateCode()
    {
        $this->assertSame('MIMI', $this->subject->createCode());
    }

    public function testCryptUneasy()
    {
        global $plugin_cf;

        $plugin_cf['cryptographp']['crypt_easy'] = '';
        $this->assertSame('RRRR', (new CodeGenerator)->createCode());
    }
}
