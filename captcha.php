<?php

/**
 * Captcha of Cryptographp_XH.
 *
 * Copyright (c) 2006-2007 Sylvain Brison
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the (x)html block element displaying the captcha,
 * the input field for the captcha code and all other elements,
 * that are related directly to the captcha,
 * such as an reload and an audio button.
 *
 * @return string
 */
function cryptographp_captcha_display() {
    global $pth, $plugin_tx;

    $dir = $pth['folder']['plugins'].'cryptographp/';
    $ptx = $plugin_tx['cryptographp'];
    $o = '<div class="cryptographp">'."\n";
    $alt = htmlspecialchars($ptx['alt_image'], ENT_QUOTES);
    $o .= tag('img id="cryptogram" src="'.$dir.'cryptographp.php" alt="'.$alt.'"')."\n";
    $alt = htmlspecialchars($ptx['alt_audio'], ENT_QUOTES);
    $o .= '<a href="'.$dir.'audio.php">'
	    .tag('img src="'.$dir.'images/audio.png" alt="'.$alt.'" title="'.$alt.'"').'</a>'."\n";
    $alt = htmlspecialchars($ptx['alt_reload'], ENT_QUOTES);
    $o .= '<a href="javascript:void(0)" onclick="document.getElementById(\'cryptogram\').src = \''.$dir.'cryptographp.php?\' + new Date().getTime()">'
	    .tag('img src="'.$dir.'images/reload.png" alt="'.$alt.'" title="'.$alt.'"').'</a>'."\n"
	    .'<div>'.$ptx['message_enter_code'].'</div>'."\n"
	    .tag('input type="text" name="cryptographp-captcha"').'</div>'."\n";
    return $o;
}


/**
 * Returns wether the correct captcha code was entered.
 *
 * @return bool
 */
function cryptographp_captcha_check() {
    global $pth;

    $code = stsl($_POST['cryptographp-captcha']);
    include $pth['folder']['plugins'].'cryptographp/config/cryptographp.cfg.php';
    $code = addslashes($code);
    $code = str_replace(' ', '', $code);  // supprime les espaces saisis par erreur.
    if (isset($_SESSION['cryptographp_code']) && $_SESSION['cryptographp_code'] == $code) {
	unset($_SESSION['cryptographp_reload']);
	if ($cryptoneuse) {unset($_SESSION['cryptographp_code']);}
	return true;
    } else {
	$_SESSION['cryptographp_reload'] = true;
	return false;
    }
}


if (!isset($plugin_tx['cryptographp'])) {
    include $pth['folder']['plugins'].'cryptographp/languages/'.$sl.'.php'; // TODO
}
if (session_id() == '') {session_start();}
//$_SESSION['cryptographp_tx'] = $plugin_tx['cryptographp'];
//session_destroy();
var_dump($_SESSION);

?>
