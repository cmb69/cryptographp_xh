<?php

/**
 * Audio CAPTCHA of Cryptographp_XH
 *
 * Copyright (c) 2012 Christoph M.Becker (see license.txt)
 */


if (session_id() == '') {session_start();}
$id = $_GET['id'];
$lang = basename($_GET['lang']);

if (!isset($_SESSION['cryptographp_code'][$id])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$code = $_SESSION['cryptographp_code'][$id];

$o = '';
for ($i = 0; $i < strlen($code); $i++) {
    $cnt = file_get_contents('./languages/'.$lang.'/'.strtolower($code[$i]).'.mp3');
    if ($cnt !== FALSE) {
        $o .= $cnt;
    } else {
        include './languages/default.php';
        include './languages/'.$lang.'.php';
        exit($plugin_tx['cryptographp']['error_audio']);
    }
}

header('Content-Type: audio/mpeg');
if (isset($_GET['download'])) {
    header('Content-Disposition: attachment; filename=captcha.mp3');
}
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.strlen($o));
echo $o;

?>
