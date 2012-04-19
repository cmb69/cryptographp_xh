<?php

/**
 * Captcha of Cryptographp_XH.
 *
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// TODO declare globals


$cryptinstall = $pth['folder']['plugins'].'cryptographp/crypt/cryptographp.fct.php';
include $cryptinstall;
if (isset($plugin_tx['cryptographp'])) {
    $_SESSION['cryptographp_tx'] = $plugin_tx['cryptographp'];
} else {
    trigger_error('Cryptograph_XH not yet loaded', E_USER_WARNING);
}


/**
 * Returns (x)html block element displaying the captcha.
 *
 * @return string
 */
function cryptographp_captcha_display() {
    global $plugin_tx;

    $ptx =& $plugin_tx['cryptographp'];

    cryptographp_update_config();
    return '<div class="captcha">'.dsp_crypt('cmsimple.cfg.php', $ptx['message_reload'])
	    .'<div>'.$ptx['message_enter_code'].'</div>'
	    .tag('input type="text" name="cryptographp-captcha"').'</div>'."\n";
}


/**
 * Returns wether the correct captcha code was entered.
 *
 * @return bool
 */
function cryptographp_captcha_check() {
    return chk_crypt(stsl($_POST['cryptographp-captcha']));
}

?>
