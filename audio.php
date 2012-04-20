<?php

// http://www.splitbrain.org/blog/2006-11/15-joining_wavs_with_php
function joinwavs($wavs){
    $fields = join('/',array( 'H8ChunkID', 'VChunkSize', 'H8Format',
                              'H8Subchunk1ID', 'VSubchunk1Size',
                              'vAudioFormat', 'vNumChannels', 'VSampleRate',
                              'VByteRate', 'vBlockAlign', 'vBitsPerSample' ));
    $data = '';
    foreach($wavs as $wav){
        $fp     = fopen($wav,'rb');
        $header = fread($fp,36);
        $info   = unpack($fields,$header);
        // read optional extra stuff
        if($info['Subchunk1Size'] > 16){
            $header .= fread($fp,($info['Subchunk1Size']-16));
        }
        // read SubChunk2ID
        $header .= fread($fp,4);
        // read Subchunk2Size
        $size  = unpack('vsize',fread($fp, 4));
        $size  = $size['size'];
        // read data
        $data .= fread($fp,$size);
    }
    return $header.pack('V',strlen($data)).$data;
}

if (session_id() == '') {session_start();}
$code = preg_split('/(?<!^)(?!$)/u', $_SESSION['cryptcode']);
$wavs = array();
foreach ($code as $char) {
    $wavs[] = './languages/en/'.$char.'.wav';
}

header('Content-type: audio/x-wav');
echo joinwavs($wavs);

?>
