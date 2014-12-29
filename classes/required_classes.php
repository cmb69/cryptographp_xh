<?php

/**
 * The autoloader.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Cryptographp
 * @author    Sylvain Brison <cryptographp@alphpa.com>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */

/**
 * Autoloads the plugin classes.
 *
 * @param string $class A class name.
 *
 * @return void
 *
 * @global array The paths of system files and folders.
 */
function Cryptographp_autoload($class)
{
    global $pth;

    $parts = explode('_', $class, 2);
    if ($parts[0] == 'Cryptographp') {
        include_once $pth['folder']['plugins'] . 'cryptographp/classes/'
            . $parts[1] . '.php';
    }
}

spl_autoload_register('Cryptographp_autoload');

?>
