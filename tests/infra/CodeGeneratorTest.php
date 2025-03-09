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
    public function testCreateCode()
    {
        $subject = $this->getMockBuilder(CodeGenerator::class)
            ->setConstructorArgs([[
                'char_allowed' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
                'char_allowed_consonants' => 'BCDFGHJKLMNPQRSTVWXZ',
                'char_allowed_vowels' => 'AEIOUY',
                'char_count_max' => '4',
                'char_count_min' => '4',
                'crypt_easy' => 'true',
            ]])
            ->onlyMethods(["randomOffset", "randomBool"])
            ->getMock();
        $this->assertSame('BABA', $subject->createCode());
    }

    public function testCryptUneasy()
    {
        $subject = $this->getMockBuilder(CodeGenerator::class)
            ->setConstructorArgs([[
                'char_allowed' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
                'char_allowed_consonants' => 'BCDFGHJKLMNPQRSTVWXZ',
                'char_allowed_vowels' => 'AEIOUY',
                'char_count_max' => '4',
                'char_count_min' => '4',
                'crypt_easy' => '',
            ]])
            ->onlyMethods(["randomOffset", "randomBool"])
            ->getMock();
        $this->assertSame('AAAA', $subject->createCode());
    }

    public function testEmptyCharAllowed(): void
    {
        $subject = $this->getMockBuilder(CodeGenerator::class)
            ->setConstructorArgs([[
                'char_allowed' => '',
                'char_allowed_consonants' => '',
                'char_allowed_vowels' => '',
                'char_count_max' => '4',
                'char_count_min' => '4',
                'crypt_easy' => '',
            ]])
            ->onlyMethods(["randomOffset", "randomBool"])
            ->getMock();
        $this->assertSame('AAAA', $subject->createCode());
    }
}
