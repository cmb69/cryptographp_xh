<?php

/**
 * Copyright 2017-2021 Christoph M. Becker
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

namespace Cryptographp\Infra;

use PHPUnit\Framework\TestCase;

class CodeGeneratorTest extends TestCase
{
    /**
     * @var CodeGenerator
     */
    private $subject;

    public function setUp(): void
    {
        mt_srand(12345);
        $this->subject = new CodeGenerator([
            'char_allowed' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
            'char_allowed_consonants' => 'BCDFGHJKLMNPQRSTVWXZ',
            'char_allowed_vowels' => 'AEIOUY',
            'char_count_max' => '4',
            'char_count_min' => '4',
            'crypt_easy' => 'true',
        ]);
    }

    public function testCreateCode()
    {
        $this->assertSame('HOGE', $this->subject->createCode());
    }

    public function testCryptUneasy()
    {
        $subject = new CodeGenerator([
            'char_allowed' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
            'char_allowed_consonants' => 'BCDFGHJKLMNPQRSTVWXZ',
            'char_allowed_vowels' => 'AEIOUY',
            'char_count_max' => '4',
            'char_count_min' => '4',
            'crypt_easy' => '',
        ]);
        $this->assertSame('7BEK', $subject->createCode());
    }
}
