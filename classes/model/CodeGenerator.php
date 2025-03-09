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

namespace Cryptographp\Model;

class CodeGenerator
{
    /** @var array<string,string> */
    private $config;

    /** @param array<string,string> $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createCode(): string
    {
        $code = '';
        $isVowel = $this->randomBool();
        $count = $this->randomCount();
        for ($i = 0; $i < $count; $i++) {
            if ($this->config['crypt_easy']) {
                if ($isVowel) {
                    $allowed = $this->config['char_allowed_vowels'] ?: "AEIOUY";
                } else {
                    $allowed = $this->config['char_allowed_consonants'] ?: "BCDFGHJKLMNPQRSTVWXZ";
                }
            } else {
                $allowed = $this->config['char_allowed'] ?: "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
            }
            $code .= $this->getRandomCharOf($allowed);
            $isVowel = !$isVowel;
        }
        return $code;
    }

    private function getRandomCharOf(string $string): string
    {
        return $string[$this->randomOffset($string)];
    }

    protected function randomOffset(string $string): int
    {
        assert(strlen($string) >= 1);
        return random_int(0, strlen($string) - 1);
    }

    protected function randomBool(): bool
    {
        return (bool) random_int(0, 1);
    }

    protected function randomCount(): int
    {
        $min = (int) $this->config['char_count_min'];
        $max = (int) $this->config['char_count_max'];
        if ($min === $max) {
            return $min;
        }
        return random_int($min, $max);
    }

    /** @codeCoverageIgnore */
    public function randomKey(): string
    {
        return random_bytes(15);
    }
}
