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
    /**
     * @var array<string,mixed>
     */
    private $data = array();

    /**
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param mixed[] $args
     * @return string
     */
    public function __call($name, array $args)
    {
        return $this->escape($this->data[$name]);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function text($key)
    {
        global $plugin_tx;

        $args = func_get_args();
        array_shift($args);
        return $this->escape(vsprintf($plugin_tx['cryptographp'][$key], $args));
    }

    /**
     * @param string $key
     * @param int $count
     * @return string
     */
    protected function plural($key, $count)
    {
        global $plugin_tx;

        if ($count == 0) {
            $key .= '_0';
        } else {
            $key .= XH_numberSuffix($count);
        }
        $args = func_get_args();
        array_shift($args);
        return $this->escape(vsprintf($plugin_tx['cryptographp'][$key], $args));
    }

    /**
     * @param string $template
     * @param array<string,mixed> $data
     * @return string
     */
    public function render($template, array $data)
    {
        global $pth;

        $this->data = $data;
        ob_start();
        echo "<!-- {$template} -->", PHP_EOL;
        include "{$pth['folder']['plugins']}cryptographp/views/{$template}.php";
        return ob_get_clean();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function escape($value)
    {
        if ($value instanceof HtmlString || $value instanceof View) {
            return $value;
        } else {
            return XH_hsc($value);
        }
    }
}
