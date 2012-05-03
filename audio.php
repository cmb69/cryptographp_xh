<?php

/**
 * Audio CAPTCHA of Cryptographp_XH
 *
 * Copyright (c) 2012 Christoph M.Becker (see license.txt)
 */


/**
 * Returns the .wav file.
// http://www.splitbrain.org/blog/2006-11/15-joining_wavs_with_php
 *
 * @param array $wavs
 * @return string
 */
function joinwavs($wavs) {
    $fields = implode('/', array('H8ChunkID', 'VChunkSize', 'H8Format',
            'H8Subchunk1ID', 'VSubchunk1Size', 'vAudioFormat', 'vNumChannels',
            'VSampleRate', 'VByteRate', 'vBlockAlign', 'vBitsPerSample'));
    $data = '';
    foreach($wavs as $wav) {
        $fp = fopen($wav, 'rb');
        $header = fread($fp,36);
        $info = unpack($fields,$header);
        // read optional extra stuff
        if($info['Subchunk1Size'] > 16) {
            $header .= fread($fp,($info['Subchunk1Size'] - 16));
        }
        // read SubChunk2ID
        $header .= fread($fp, 4);
        // read Subchunk2Size
        $size = unpack('vsize', fread($fp, 4));
        $size = $size['size'];
        // read data
        $data .= fread($fp, $size);
    }
    return $header.pack('V', strlen($data)).$data;
}


if (session_id() == '') {session_start();}
$id = $_GET['id'];
$lang = basename($_GET['lang']);
$code = preg_split('/(?<!^)(?!$)/u', $_SESSION['cryptographp_code'][$id]);

$o = '';
foreach ($code as $char) {
    $o .= file_get_contents('./languages/'.$lang.'/'.$char.'.mp3');
}

//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT");
//header("Cache-Control: no-cache");
//header("Pragma: no-cache");
//header("Cache-Control: post-check=0, pre-check=0", FALSE);
header('Content-Type: audio/mpeg');
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.strlen($o));
echo $o;

//$wavs = array();

//include './mp3.php';
//$mp3 = new mp3('./languages/en/'.$code[0].'.mp3');
//for ($i = 1; $i < count($code); $i++) {
//    $mp3f = new mp3('./languages/'.$lang.'/'.strtolower($code[$i]).'.mp3');
//    $mp3->mergebehind($mp3f);
//    $mp3->striptags();
//}
//$mp3->output('captcha.mp3');

//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT");
//header("Cache-Control: no-cache");
//header("Pragma: no-cache");
//header("Cache-Control: post-check=0, pre-check=0", FALSE);

//header('Content-Type: audio/wav');

//header('Content-Disposition: attachment; filename=captcha.wav');

//header('Content-Transfer-Encoding: binary');
//header('Content-Length: '.strlen($data));
//echo $data;

?>
