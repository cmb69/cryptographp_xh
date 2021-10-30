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

class Url
{
    /**
     * @var string
     */
    private $path;

    /** @var string */
    private $page;

    /**
     * @var array<string,string>
     */
    private $params;

    /**
     * @return self
     */
    public static function current()
    {
        global $sn, $su;

        if ($su) {
            $params = array_slice($_GET, 1);
        } else {
            $params = $_GET;
        }
        return new self($sn, $su, $params);
    }

    /**
     * @param string $path
     * @param string $page
     * @param array<string,string> $params
     */
    private function __construct($path, $page, array $params)
    {
        $this->path = $path;
        $this->page = $page;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function relative()
    {
        $result = $this->path;
        if (($query = $this->query())) {
            $result = "?$query";
        }
        return $result;
    }

    /**
     * @return string
     */
    public function absolute()
    {
        $result = CMSIMPLE_URL;
        if (($query = $this->query())) {
            $result = "?$query";
        }
        return $result;
    }

    /**
     * @return string
     */
    private function query()
    {
        $result = "{$this->page}";
        $additional = preg_replace('/=(?=&|$)/', "", http_build_query($this->params, "", "&"));
        if ($additional) {
            $result .= "&$additional";
        }
        return $result;
    }

    /**
     * @param string $param
     * @param string $value
     * @return self
     */
    public function with($param, $value)
    {
        $params = $this->params;
        $params[$param] = (string) $value;
        return new self($this->path, $this->page, $params);
    }

    /**
     * @param string $param
     * @return self
     */
    public function without($param)
    {
        $params = $this->params;
        unset($params[$param]);
        return new self($this->path, $this->page, $params);
    }
}
