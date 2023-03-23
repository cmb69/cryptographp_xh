<?php

/**
 * 2021 Christoph M. Becker
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

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class CodeStoreTest extends TestCase
{
    /** @var int */
    private $timestamp;

    /** @var int */
    private $retention;

    /** @var string */
    private $key;

    /** @var string */
    private $code;

    /** @var CodeStore */
    private $subject;

    public function setUp(): void
    {
        vfsStream::setup("test");
        $this->timestamp = 123456;
        $this->retention = 600;
        $this->key = "012345678901234";
        $this->code = "helo";
        $this->subject = new CodeStore(vfsStream::url("test/store"), $this->timestamp, $this->retention);
    }

    public function testNonExistent()
    {
        $this->assertNull($this->subject->find($this->key));
    }

    public function testCanReadWritten()
    {
        $this->subject->put($this->key, $this->code);
        $this->assertSame($this->code, $this->subject->find($this->key));
    }

    public function testSetTwice()
    {
        $this->subject->put($this->key, "hola");
        $this->subject->put($this->key, $this->code);
        $this->assertSame($this->code, $this->subject->find($this->key));
    }

    public function testHashCollision()
    {
        $otherKey = "012345678901235";
        $otherCode = "hola";
        $this->subject->put($this->key, $this->code);
        $this->subject->put($otherKey, $otherCode);
        $this->assertSame($this->code, $this->subject->find($this->key));
        $this->assertSame($otherCode, $this->subject->find($otherKey));
    }

    public function testTableFullRebuilds()
    {
        $suffix = "345678901234";
        for ($i = 0; $i < 129; $i++) {
            $this->subject->put(sprintf("%03d%s", $i, $suffix), $this->code);
        }
        for ($i = 0; $i < 129; $i++) {
            $this->assertSame($this->subject->find(sprintf("%03d%s", $i, $suffix)), $this->code);
        }
    }

    public function testInvalidate()
    {
        $otherTimestamp = $this->timestamp + $this->retention;
        $this->subject->put($this->key, $this->code);
        $pht = new CodeStore(vfsStream::url("test/store"), $otherTimestamp, $this->retention);
        $this->assertSame($this->code, $pht->find($this->key));
        $pht->invalidate($this->key);
        $this->assertNull($pht->find($this->key));
    }
}
