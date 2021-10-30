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

class View
{
    /** @var string $templateDir */
    private $templateDir;

    /** @var array<string,string> */
    private $lang;

    /**
     * @param string $templateDir
     * @param array<string,string> $lang
     */
    public function __construct($templateDir, $lang)
    {
        $this->templateDir = $templateDir;
        $this->lang = $lang;
    }

    /**
     * @param string $key
     * @return string
     */
    public function text($key)
    {
        $args = func_get_args();
        array_shift($args);
        return $this->esc(vsprintf($this->lang[$key], $args));
    }

    /**
     * @param string $key
     * @param int $count
     * @return string
     */
    public function plural($key, $count)
    {
        if ($count == 0) {
            $key .= '_0';
        } else {
            $key .= XH_numberSuffix($count);
        }
        $args = func_get_args();
        array_shift($args);
        return $this->esc(vsprintf($this->lang[$key], $args));
    }

    /**
     * @param string $_template
     * @param array<string,mixed> $_data
     * @return string
     */
    public function render($_template, array $_data)
    {
        extract($_data);
        ob_start();
        echo "<!-- {$_template} -->", PHP_EOL;
        include "{$this->templateDir}/{$_template}.php";
        return ob_get_clean();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function esc($value)
    {
        if ($value instanceof HtmlString || $value instanceof View) {
            return $value;
        } else {
            return XH_hsc($value);
        }
    }
}
