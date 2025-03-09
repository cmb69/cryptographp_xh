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

namespace Cryptographp\Model;

use PHPUnit\Framework\TestCase;

class AudioCaptchaTest extends TestCase
{
    public function testCreatesWav()
    {
        $sut = $this->sut();
        $wav = $sut->createWav("en", "gevo");
        $this->assertStringEqualsFile(__DIR__ . "/../audios/gevo.wav", $wav);
    }

    public function testCreatesEnglishWavIfTranslationIsMissing()
    {
        $sut = $this->sut();
        $wav = $sut->createWav("de", "gevo");
        $this->assertStringEqualsFile(__DIR__ . "/../audios/gevo.wav", $wav);
    }

    public function testFailsToCreateWav()
    {
        $sut = $this->sut();
        $wav = $sut->createWav("en", "!");
        $this->assertNull($wav);
    }

    private function sut()
    {
        return new FakeAudioCaptcha("./languages/");
    }
}
