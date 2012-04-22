<?php

/**
 * Back-end of Cryptographp_XH.
 *
 * Copyright (c) 2006-2007 Sylvain Brison
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns (x)html plugin version information.
 *
 * @return string
 */
function cryptographp_version() {
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Cryptographp_XH">Cryptographp_XH</a></h1>'."\n"
	    .tag('img class="cryptographp_plugin_icon" src="'.$pth['folder']['plugins'].'cryptographp/cryptographp.png" alt="Plugin icon"')."\n"
	    .'<p style="margin-top: 1em">Version: '.CRYPTOGRAPHP_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2006-2007 <a href="http://www.captcha.fr/">Sylvain Brison</a>'.tag('br')
	    .'Copyright &copy; 2011-2012 <a href="http://3-magi.net/">Christoph M. Becker</a></p>'."\n"
	    .'<p class="cryptographp_license">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p class="cryptographp_license">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p class="cryptographp_license">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function cryptographp_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('CRYPTOGRAPHP_PHP_VERSION', '4.3.0');
    $ptx = $plugin_tx['cryptographp'];
    $imgdir = $pth['folder']['plugins'].'cryptographp/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $o = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, CRYPTOGRAPHP_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], CRYPTOGRAPHP_PHP_VERSION)
	    .tag('br')."\n";
    foreach (array('date', 'gd', 'session') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br').tag('br')."\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folder = $pth['folder']['plugins'].'cryptographp/'.$folder;
	$o .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $o;
}


/**
 * Handle the plugin's administration.
 */
if (!empty($cryptographp)) {
    $o .= print_plugin_admin('off');
    switch ($admin) {
	case '':
	    $o .= cryptographp_version().tag('hr').cryptographp_system_check();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
