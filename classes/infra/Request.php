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

namespace Cryptographp\Infra;

use Cryptographp\Value\Url;

class Request
{
    /** @codeCoverageIgnore */
    public static function current(): self
    {
        return new self;
    }

    public function url(): Url
    {
        $rest = $this->query();
        if ($rest !== "") {
            $rest = "?" . $rest;
        }
        return Url::from(CMSIMPLE_URL . $rest);
    }

    public function action(): string
    {
        if (!isset($_GET["cryptographp_action"]) || !is_string($_GET["cryptographp_action"])) {
            return "";
        }
        return $_GET["cryptographp_action"];
    }

    /** @return array{string,string} */
    public function captchaPost(): array
    {
        return [
            $this->trimmedPostString("cryptographp-captcha"),
            $this->trimmedPostString("cryptographp_nonce"),
        ];
    }

    private function trimmedPostString(string $key): string
    {
        $post = $this->post();
        return isset($post[$key]) && is_string($post[$key]) ? trim($post[$key]) : "";
    }

    /** @codeCoverageIgnore */
    public function sl(): string
    {
        global $sl;
        return $sl;
    }

    /** @codeCoverageIgnore */
    protected function query(): string
    {
        return $_SERVER["QUERY_STRING"];
    }

    /**
     * @return array<string|array<string>>
     * @codeCoverageIgnore
     */
    protected function post(): array
    {
        return $_POST;
    }
}
