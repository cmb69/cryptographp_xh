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


define('CRYPTOGRAPHP_VERSION', '1beta2');


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
 * Returns the php configuration.
 *
 * @return string
 */
function cryptograph_config() {
    global $plugin_cf;

$crypthographp_config = array(
    'crypt_width' => array('$cryptwidth', 'int'),
    'crypt_height' => array('$cryptheight', 'int'),
    'bg_rgb_red' => array('$bgR', 'int'),
    'bg_rgb_green' => array('$bgG', 'int'),
    'bg_rgb_blue' => array('$bgB', 'int'),
    'bg_clear' => array('$bgclear', 'bool'),
    'bg_image' => array('$bgimg', 'string'),
    'bg_frame' => array('$bgframe', 'bool'),
    'char_rgb_red' => array('$charR', 'int'),
    'char_rgb_green' => array('$charG', 'int'),
    'char_rgb_blue' => array('$charB', 'int'),
    'char_color_random' => array('$charcolorrnd', 'bool'),
    'char_color_random_level' => array('$charcolorrndlevel', 'int'),
    'char_clear' => array('$charclear', 'int'),
    'fonts' => array('$tfont', 'array'),
    'char_allowed' => array('$charel', 'string'),
    'crypt_easy' => array('$crypteasy', 'bool'),
    'char_allowed_consonants' => array('$charelc', 'string'),
    'char_allowed_vowels' => array('$charelv', 'string'),
    'char_count_min' => array('$charnbmin', 'int'),
    'char_count_max' => array('$charnbmax', 'int'),
    'char_space' => array('$charspace', 'int'),
    'char_size_min' => array('$charsizemin', 'int'),
    'char_size_max' => array('$charsizemax', 'int'),
    'char_angle_max' => array('$charanglemax', 'int'),
    'char_displace' => array('$charup', 'bool'),
    'crypt_gaussian_blur' => array('$cryptgaussianblur', 'bool'),
    'crypt_gray_scale' => array('$cryptgrayscal', 'bool'),
    'noise_pixel_min' => array('$noisepxmin', 'int'),
    'noise_pixel_max' => array('$noisepxmax', 'int'),
    'noise_line_min' => array('$noiselinemin', 'int'),
    'noise_line_max' => array('$noiselinemax', 'int'),
    'noise_circle_min' => array('$nbcirclemin', 'int'),
    'noise_circle_max' => array('$nbcirclemax', 'int'),
    'noise_color_char' => array('$noisecolorchar', 'int'),
    'noise_brush_size' => array('$brushsize', 'int'),
    'noise_above' => array('$noiseup', 'bool'),
    'crypt_format' => array('$cryptformat', 'string'),
    'crypt_use_timer' => array('$cryptusetimer', 'int'),
    'crypt_use_timer_error' => array('$cryptusertimererror', 'bool'),
);

    $pcf = $plugin_cf['cryptographp'];
    $res = '<?php'."\n\n"
	    .'// This file was automatically generated by Cryptographp_XH.'."\n\n";
    foreach ($crypthographp_config as $key => $config) {
	list($varname, $type) = $config;
	switch ($type) { // TODO: on errors use default?
	    case 'int':
		$val = $pcf[$key];
		break;
	    case 'bool':
		$val = strtolower($pcf[$key] == 'yes') ? 'true' : 'false';
		break;
	    case 'string':
		$val = '\''.addcslashes($pcf[$key], '\'\\').'\'';
		break;
	    case 'array':
		$val = 'array(\''.implode('\', \'', explode(';', $pcf[$key])).'\')';
		break;
	}
	$res .= $varname.' = '.$val.';'."\n";
    }
    $res .= "\n".'?>'."\n";
    return $res;
}


/**
 * Updates the configuration, if necessary.
 *
 * @return void
 */
function cryptographp_update_config() {
    global $pth;

    $fn = $pth['folder']['plugins'].'cryptographp/config/cryptographp.cfg.php';
    if (filemtime($pth['folder']['plugins'].'cryptographp/config/config.php') > filemtime($fn)) {
	if (($fh = fopen($fn, 'w')) === FALSE || fwrite($fh, cryptograph_config()) === FALSE) {
	    e('cntsave', 'file', $fn);
	}
	if ($fh !== FALSE) {
	    fclose($fh);
	}
    }
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


/**
 * Update the configuration file.
 */
cryptographp_update_config();

?>
