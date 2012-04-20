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

// TODO declare globals


//$cryptinstall = $pth['folder']['plugins'].'cryptographp/cryptographp.fct.php';
//include $cryptinstall;
// TODO: hmm...
if (isset($plugin_tx['cryptographp'])) {
    $_SESSION['cryptographp_tx'] = $plugin_tx['cryptographp'];
} else {
    trigger_error('Cryptograph_XH not yet loaded', E_USER_WARNING);
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
    $_SESSION['cryptdir'] = $dir;
    $ptx = $plugin_tx['cryptographp'];
    return '<div class="captcha">'
	    .tag('img id="cryptogram" src="'.$dir.'cryptographp.php"')."\n"
	    .'<a href="'.$dir.'audio.php">'.tag('img src="'.$dir.'images/audio.png"').'</a>'."\n"
	    .'<a href="javascript:document.images.cryptogram.src = \''.$dir.'cryptographp.php\'">'
	    .tag('img src="'.$dir.'images/reload.png"').'</a>'."\n"
	    .'<div>'.$ptx['message_enter_code'].'</div>'
	    .tag('input type="text" name="cryptographp-captcha"').'</div>'."\n";
}


/**
 * Returns wether the correct captcha code was entered.
 *
 * @return bool
 */
function cryptographp_captcha_check() {
    $code = stsl($_POST['cryptographp-captcha']);
 include ($_SESSION['configfile']);
 $code = addslashes ($code);
 $code = str_replace(' ','',$code);  // supprime les espaces saisis par erreur.
 $code = ($difuplow?$code:strtoupper($code));
 switch (strtoupper($cryptsecure)) {
        case "MD5"  : $code = md5($code); break;
        case "SHA1" : $code = sha1($code); break;
        }
 if ($_SESSION['cryptcode'] and ($_SESSION['cryptcode'] == $code))
    {
    unset($_SESSION['cryptreload']);
    if ($cryptoneuse) unset($_SESSION['cryptcode']);
    return true;
    }
    else {
         $_SESSION['cryptreload']= true;
         return false;
         }
}

?>
