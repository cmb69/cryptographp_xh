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
 * Returns code to embed the flash player.
 */
function cryptographp_player() {
    global $hjs, $pth, $sl, $plugin_cf;

    $dir = $pth['folder']['plugins'].'cryptographp/';
    $get = 'id='.$_SESSION['cryptographp_id'].'&lang='.$sl;
    $audio = $dir.'audio.php?'.$get;
    //$audio = $dir.'captcha.mp3';
    include_once $pth['folder']['plugins'].'jquery/jquery.inc.php';
    include_jquery();
    include_jqueryplugin('jplayer', $pth['folder']['plugins'].'cryptographp/jquery.jplayer.min.js');
    $hjs .= <<<SCRIPT
<script type="text/javascript">
$(document).ready(function(){
	$("#player").jPlayer({
		ready: function () {
			$(this).jPlayer("setMedia", {
				mp3: "$audio"
			})/*.jPlayer("play")*/;
		},
		supplied: "mp3",
		swfPath: "{$dir}Jplayer.swf",
		solution: "flash,html"
	});
});
</script>

SCRIPT;
    $o = '<span id="player"></span>';
    return $o;
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
    global $pth, $sl, $plugin_tx;

    if (!isset($plugin_tx['cryptographp'])) {
	include $pth['folder']['plugins'].'cryptographp/languages/'.$sl.'.php';
    }
    $_SESSION['cryptographp_id'] = isset($_SESSION['cryptographp_id']) ? $_SESSION['cryptographp_id'] + 1 : 1;
    $_SESSION['cryptographp_lang'][$_SESSION['cryptographp_id']] = $sl;
    $dir = $pth['folder']['plugins'].'cryptographp/';
    $ptx = $plugin_tx['cryptographp'];
    $get = 'id='.$_SESSION['cryptographp_id'].'&amp;lang='.$sl;
    $o = '<div class="cryptographp">'."\n";
    $alt = htmlspecialchars($ptx['alt_image'], ENT_QUOTES);
    $o .= tag('img id="cryptographp'.$_SESSION['cryptographp_id'].'" src="'
	    .$dir.'cryptographp.php?'.$get.'" alt="'.$alt.'"')."\n";
    $o .= cryptographp_player();
    $alt = htmlspecialchars($ptx['alt_audio'], ENT_QUOTES);
    $o .= '<a href="javascript:void(0)" onclick="$(\'#player\').jPlayer(\'play\', 0)">'
	    .tag('img src="'.$dir.'images/audio.png" alt="'.$alt.'" title="'.$alt.'"').'</a>'."\n";
    $alt = htmlspecialchars($ptx['alt_reload'], ENT_QUOTES);
    $o .= '<a href="javascript:void(0)" onclick="document.getElementById(\'cryptographp'.$_SESSION['cryptographp_id'].'\').src = \''
		.$dir.'cryptographp.php?id='.$_SESSION['cryptographp_id'].'&lang='.$sl.'&\' + new Date().getTime()">';
    $o .= tag('img src="'.$dir.'images/reload.png" alt="'.$alt.'" title="'.$alt.'"').'</a>'."\n"
	    .'<div>'.$ptx['message_enter_code'].'</div>'."\n"
	    .tag('input type="text" name="cryptographp-captcha"')
	    .tag('input type="hidden" name="cryptographp_id" value="'.$_SESSION['cryptographp_id'].'"')
	    .'</div>'."\n";
    return $o;
}


/**
 * Returns whether the correct captcha code was entered.
 *
 * @return bool
 */
function cryptographp_captcha_check() {
    global $pth, $plugin_cf;

    if (!isset($plugin_cf['cryptographp'])) {
	include $pth['folder']['plugins'].'cryptographp/config/config.php';
    }
    $id = stsl($_POST['cryptographp_id']);
    $code = stsl($_POST['cryptographp-captcha']);
    $ok = isset($_SESSION['cryptographp_code'][$id])
	    && $_SESSION['cryptographp_code'][$id] == $code
	    && $_SESSION['cryptographp_time'][$id] + $plugin_cf['cryptographp']['crypt_expiration'] >= time();
    unset($_SESSION['cryptographp_code'][$id], $_SESSION['crytographp_lang'][$id],
	    $_SESSION['cryptographp_time'][$id]);
    return $ok;
}


/**
 * Start the session.
 */
if (session_id() == '') {session_start();}

?>
