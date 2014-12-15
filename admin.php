<?php

/**
 * Back-end of Cryptographp_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Crpytographp
 * @author    Sylvain Brison <cryptographp@alphpa.com>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Renders the plugin information.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 */
function Cryptographp_version()
{
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Cryptographp_XH">'
        . ' Cryptographp_XH</a></h1>' . "\n"
        . tag(
            'img class="cryptographp_plugin_icon" src="' . $pth['folder']['plugins']
            . 'cryptographp/cryptographp.png" alt="Plugin icon"'
        ) . "\n"
        . '<p style="margin-top: 1em">Version: ' . CRYPTOGRAPHP_VERSION . '</p>'
        . "\n"
        . '<p>Copyright &copy; 2006-2007 <a href="http://www.captcha.fr/">'
        . 'Sylvain Brison</a>' . tag('br')
        . 'Copyright &copy; 2011-2012 <a href="http://3-magi.net/">'
        . 'Christoph M. Becker</a></p>' . "\n"
        . '<p class="cryptographp_license">'
        . 'This program is free software: you can redistribute it and/or modify'
        . ' it under the terms of the GNU General Public License as published by'
        . ' the Free Software Foundation, either version 3 of the License, or'
        . ' (at your option) any later version.</p>' . "\n"
        . '<p class="cryptographp_license">'
        . 'This program is distributed in the hope that it will be useful,'
        . ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
        . ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
        . ' GNU General Public License for more details.</p>' . "\n"
        . '<p class="cryptographp_license">'
        . 'You should have received a copy of the GNU General Public License'
        . ' along with this program.  If not, see'
        . ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/'
        . '</a>.</p>' . "\n";
}

/**
 * Renderns the system check.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Cryptographp_systemCheck()
{
    global $pth, $tx, $plugin_tx;

    define('CRYPTOGRAPHP_PHP_VERSION', '4.3.0');
    $ptx = $plugin_tx['cryptographp'];
    $imgdir = $pth['folder']['plugins'].'cryptographp/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
        . (version_compare(PHP_VERSION, CRYPTOGRAPHP_PHP_VERSION) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf(
            $ptx['syscheck_phpversion'], CRYPTOGRAPHP_PHP_VERSION
        )
        . tag('br') . "\n";
    foreach (array('date', 'gd', 'pcre', 'session') as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            .tag('br') . "\n";
    }
    if (function_exists('gd_info')) {
        $gdinfo = gd_info();
        if (!isset($gdinfo['JPEG Support'])) {
            $gdinfo['JPEG Support'] = $gdinfo['JPG support'];
        }
        $support = array(
            array('FreeType Support', 'freetype'),
            array('GIF Create Support', 'gif'),
            array('JPEG Support', 'jpeg'),
            array('PNG Support', 'png')
        );
        foreach ($support as $i => $key) {
            $o .= ($gdinfo[$key[0]] ? $ok : ($i < 1 ? $fail : $warn))
                . '&nbsp;&nbsp;' . $ptx['syscheck_' . $key[1] . '_support']
                . tag('br') . "\n";
        }
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
        . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes']
        . tag('br') . tag('br') . "\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding']
        . tag('br') . tag('br') . "\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folder = $pth['folder']['plugins'] . 'cryptographp/' . $folder;
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . tag('br') . "\n";
    }
    return $o;
}

/*
 * Handle the plugin administration.
 */
if (!empty($cryptographp)) {
    $o .= print_plugin_admin('off');
    switch ($admin) {
    case '':
        $o .= Cryptographp_version() . tag('hr') . Cryptographp_systemCheck();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
