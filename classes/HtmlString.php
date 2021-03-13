<?php

/**
 * Copyright 2016-2021 Christoph M. Becker
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

class HtmlString
{
    /** @var string */
    private $value;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->value = $string;
    }

    public function __toString()
    {
        return $this->value;
    }
}
