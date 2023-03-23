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

use Cryptographp\Value\Response;

class Responder
{
    /** @return string|never */
    public static function respond(Response $response): string
    {
        if ($response->forbidden()) {
            self::purgeOutputBuffers();
            header("HTTP/1.1 403 Forbidden");
            echo $response->output();
            exit;
        }
        if ($response->attachment() !== null) {
            header("Content-Disposition: attachment; filename=\"" . $response->attachment() . "\"");
        }
        if ($response->length() !== null) {
            header("Content-Length: " . $response->length());
        }
        if ($response->contentType() !== null) {
            self::purgeOutputBuffers();
            header("Content-Type: " . $response->contentType());
            echo $response->output();
            exit;
        }
        return $response->output();
    }

    /** @return void */
    private static function purgeOutputBuffers()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }
}
