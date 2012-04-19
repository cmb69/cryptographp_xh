<?php
// utf-8-marker: äöüß
header('Content-type: image/png');
$text = wordwrap(urldecode($_GET['text']), 15);
$lines = explode("\n", $text);
$font = './fonts/DejaVuSans.ttf';
$fontsize = 12;
$padding = 5;
$bbox = imagettfbbox($fontsize, 0, $font, $text);
//$width = max($strlens) * imagefontwidth($font) + 2 * $padding;
//$height = count($lines) * (imagefontheight($font) + $padding) + $padding;
$width = $bbox[2] - $bbox[0] + 1 + 2 * $padding;
$height = $bbox[1] - $bbox[7] + 1 + 2 * $padding;
//var_dump($width);
//var_dump($height);
$bbox = imagettfbbox($fontsize, 0, $font, $lines[0]);
//var_dump($bbox);
//exit;
$img = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($img, 255, 255, 255);
$fg = imagecolorallocate($img, 192, 0, 0);
imagefilledrectangle($img, 0, 0, $width-1, $height-1, $bg);
//imagerectangle($img, 0, 0, $width-1, $height-1, $fg);
imagettftext($img, $fontsize, 0, $padding, $bbox[1]-$bbox[7]+1, $fg, $font, $text);
imagepng($img);

?>
