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

namespace Cryptographp\Value;

class Response
{
    public static function create(string $output): self
    {
        $that = new self;
        $that->output = $output;
        return $that;
    }

    public static function forbid(string $output = ""): self
    {
        $that = new self;
        $that->forbidden = true;
        $that->output = $output;
        return $that;
    }

    /** @var string */
    private $output;

    /** @var string|null */
    private $bjs = null;

    /** @var bool */
    private $forbidden = false;

    /** @var string|null */
    private $contentType = null;

    /** @var string|null */
    private $attachment = null;

    /** @var int|null */
    private $length = null;

    public function withBjs(string $bjs): self
    {
        $that = clone $this;
        $that->bjs = $bjs;
        return $that;
    }

    public function withContentType(string $contentType): self
    {
        $that = clone $this;
        $that->contentType = $contentType;
        return $that;
    }

    public function withAttachment(string $attachment): self
    {
        $that = clone $this;
        $that->attachment = $attachment;
        return $that;
    }

    public function withLength(int $length): self
    {
        $that = clone $this;
        $that->length = $length;
        return $that;
    }

    public function output(): string
    {
        return $this->output;
    }

    public function bjs(): ?string
    {
        return $this->bjs;
    }

    public function forbidden(): bool
    {
        return $this->forbidden;
    }

    public function contentType(): ?string
    {
        return $this->contentType;
    }

    public function attachment(): ?string
    {
        return $this->attachment;
    }

    public function length(): ?int
    {
        return $this->length;
    }
}
