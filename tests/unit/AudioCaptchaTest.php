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

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class AudioCaptchaTest extends TestCase
{
    /**
     * @var AudioCaptcha
     */
    private $subject;

    public function setUp(): void
    {
        $this->setUpFilesystem();
        $this->subject = new AudioCaptcha('en');
    }

    private function setUpFilesystem()
    {
        global $pth;

        vfsStream::setup('test');
        $pth['folder']['plugins'] = vfsStream::url('test/');
        $folder = vfsStream::url('test/cryptographp/languages/en/');
        mkdir($folder, 0777, true);
        file_put_contents("{$folder}a.raw", 'foo');
        file_put_contents("{$folder}b.raw", 'bar');
        file_put_contents("{$folder}c.raw", 'baz');
    }

    public function testCreateWav()
    {
        $this->assertStringStartsWith('RIFF', $this->subject->createWav('abc'));
    }

    public function testCreateWavFails()
    {
        $this->assertNull($this->subject->createWav('xyz'));
    }
}
