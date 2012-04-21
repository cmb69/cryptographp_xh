<?php

/**
 * Error message as image.
 *
 * Copyright (c) 2011-2012 Christoph M. Becker (see README.txt)
 */

error_reporting(0);

$text = wordwrap(urldecode($_GET['text']), 15);
$lines = explode("\n", $text);
$font = './fonts/DejaVuSans.ttf';
$fontsize = 12;
$padding = 5;
$bbox = imagettfbbox($fontsize, 0, $font, $text);
$width = $bbox[2] - $bbox[0] + 1 + 2 * $padding;
$height = $bbox[1] - $bbox[7] + 1 + 2 * $padding;
$bbox = imagettfbbox($fontsize, 0, $font, $lines[0]);
$img = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($img, 255, 255, 255);
$fg = imagecolorallocate($img, 192, 0, 0);
imagefilledrectangle($img, 0, 0, $width-1, $height-1, $bg);
imagettftext($img, $fontsize, 0, $padding, $bbox[1]-$bbox[7]+1, $fg, $font, $text);
header('Content-type: image/png');
imagepng($img);

?>
