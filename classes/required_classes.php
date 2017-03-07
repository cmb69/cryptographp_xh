<?php

/**
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class, 2);
    if ($parts[0] == 'Cryptographp') {
        include_once dirname(__FILE__) . '/' . $parts[1] . '.php';
    }
});
