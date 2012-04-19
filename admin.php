<?php

/**
 * Back-end of Cryptographp_XH.
 * Copyright (c) 2011 Christoph M. Becker (see license.txt)
 */
 

// utf-8 marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('CRYPTOGRAPHP_PHP_VERSION', '4.3.0');


/**
 * Returns (x)html plugin version information.
 *
 * @return string
 */
function cryptographp_version() {
    return '<h1>Cryptographp_XH</h1>'."\n"
	    .'<p>Version: '.CRYPTOGRAPHP_VERSION.'</p>'."\n"
	    .'<p>Cryptographp_XH is powered by '
	    .'<a href="http://www.captcha.fr/" target="_blank">'
	    .'cryptographp</a>.</p>'."\n"
	    .'<p>Copyright &copy; 2011 Christoph M. Becker</p>'."\n"
	    .'<p style="text-align:justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align:justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align:justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Plugin administration
 */
if (isset($cryptographp)) {
    // check requirements // RELEASE-TODO
    if (version_compare(PHP_VERSION, CRYPTOGRAPHP_PHP_VERSION) < 0)
	$e .= '<li>'.sprintf($plugin_tx['cryptographp']['error_phpversion'], CRYPTOGRAPHP_PHP_VERSION).'</li>'."\n";
    foreach (array('date', 'gd', 'session') as $ext) { 
	if (!extension_loaded($ext))
	    $e .= '<li>'.sprintf($plugin_tx['cryptographp']['error_extension'], $ext).'</li>'."\n";
    }
    if (strtoupper($tx['meta']['codepage']) != 'UTF-8') {
	$e .= '<li>'.$plugin_tx['cryptographp']['error_encoding'].'</li>'."\n";
    }

    initvar('admin');
    initvar('action');
    
    $o .= print_plugin_admin('off');
    
    switch ($admin) {
	case '':
	    $o .= cryptographp_version();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
