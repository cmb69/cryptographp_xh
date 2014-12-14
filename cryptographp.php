<?php

/**
 * CAPTCHA image generator of Cryptographp_XH
 *
 * Copyright (c) 2006-2007 Sylvain Brison (cryptographp@alphpa.com)
 * Copyright (c) 2011-2012 Christoph M. Becker (see README.txt)
 */


error_reporting(0);


function error($text) {
    $text = wordwrap($text, 15);
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
    exit;
}

session_start();
$id = $_GET['id'];
$lang = basename($_GET['lang']);

include './config/cryptographp.cfg.php';
include './languages/'.$lang.'.php';

if (!isset($_SESSION['cryptographp_id'])) {
    error($plugin_tx['cryptographp']['error_cookies']);
}



$delay = time() - $_SESSION['cryptographp_time'][$id];
if ($delay < $cryptusetimer) {
    if ($cryptusertimererror) {
	error($plugin_tx['cryptographp']['error_user_time']);
    } else {
	sleep($cryptusetimer - $delay);
    }
}

// Création du cryptogramme temporaire
$imgtmp = imagecreatetruecolor($cryptwidth, $cryptheight);
$blank = imagecolorallocate($imgtmp, 255, 255, 255);
$black = imagecolorallocate($imgtmp, 0, 0, 0);
imagefill($imgtmp, 0, 0, $blank);


$word = '';
$x = 10;
$pair = rand(0, 1);
$charnb = rand($charnbmin, $charnbmax);
for ($i=1; $i <= $charnb; $i++) {
    $tword[$i]['font'] =  $tfont[array_rand($tfont, 1)];
    $tword[$i]['angle'] = rand(1, 2) == 1 ? rand(0, $charanglemax) : rand(360 - $charanglemax, 360);

    if ($crypteasy) {
	$tword[$i]['element'] = !$pair ? $charelc[rand(0, strlen($charelc) - 1)] : $charelv[rand(0, strlen($charelv) - 1)];
    } else {
	$tword[$i]['element'] = $charel[rand(0,strlen($charel)-1)];
    }

    $pair = !$pair;
    $tword[$i]['size'] = rand($charsizemin, $charsizemax);
    $lafont = 'fonts/'.$tword[$i]['font'];
    $bbox = imagettfbbox($tword[$i]['size'], $tword[$i]['angle'], $lafont, $tword[$i]['element']);
    $min = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
    $max = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
    $delta = $cryptheight - $max + $min;
    $tword[$i]['y'] = $delta / 2 + abs($min) - 1;
    if ($charup) {
	$tword[$i]['y'] += rand(-intval($delta / 2), intval($delta / 2));
    }
    $word .= $tword[$i]['element'];

    imagettftext($imgtmp, $tword[$i]['size'], $tword[$i]['angle'], $x, $tword[$i]['y'], $black, $lafont, $tword[$i]['element']);

    $x += $charspace;
}

// Calcul du racadrage horizontal du cryptogramme temporaire
$xbegin = 0;
$x = 0;
while ($x < $cryptwidth && !$xbegin) {
    $y = 0;
    while ($y < $cryptheight && !$xbegin) {
	if (imagecolorat($imgtmp, $x, $y) != $blank) {$xbegin = $x;}
	$y++;
    }
    $x++;
}

$xend = 0;
$x = $cryptwidth - 1;
while ($x > 0 && !$xend) {
    $y = 0;
    while ($y < $cryptheight && !$xend) {
	if (imagecolorat($imgtmp, $x, $y) != $blank) {$xend = $x;}
	$y++;
    }
    $x--;
}

$xvariation = round($cryptwidth / 2 - ($xend - $xbegin) / 2);
imagedestroy($imgtmp);


// Création du cryptogramme définitif
// Création du fond
$img = imagecreatetruecolor($cryptwidth, $cryptheight);

if ($bgimg && is_dir($bgimg)) {
    $dh  = opendir($bgimg);
    while (($filename = readdir($dh)) != FALSE) {
	if (preg_match('/\.(gif|jpg|png)$/', $filename)) {
	    $files[] = $filename;
	}
    }
    closedir($dh);
    $bgimg = $bgimg.'/'.$files[array_rand($files,1)];
}
if ($bgimg) {
    list($getwidth, $getheight, $gettype, $getattr) = getimagesize($bgimg);
    switch ($gettype) {
	case "1": $imgread = imagecreatefromgif($bgimg); break;
	case "2": $imgread = imagecreatefromjpeg($bgimg); break;
	case "3": $imgread = imagecreatefrompng($bgimg); break;
    }
    imagecopyresized($img, $imgread, 0, 0, 0, 0, $cryptwidth, $cryptheight, $getwidth, $getheight);
    imagedestroy($imgread);
} else {
    $bg = imagecolorallocate($img, $bgR, $bgG, $bgB);
    imagefill($img, 0, 0, $bg);
    if ($bgclear) {imagecolortransparent($img, $bg);}
}


// Création de l'écriture
function ecriture() {
    global $img, $ink, $charR, $charG, $charB, $charclear, $xvariation, $charnb,
	    $charcolorrnd, $charcolorrndlevel, $tword, $charspace;

    if (function_exists('imagecolorallocatealpha')) {
	$ink = imagecolorallocatealpha($img, $charR, $charG, $charB, $charclear);
    } else {
	$ink = imagecolorallocate ($img, $charR, $charG, $charB);
    }

    $x = $xvariation;
    for ($i=1; $i <= $charnb; $i++) {

	if ($charcolorrnd) {   // Choisit des couleurs au hasard
	    $ok = FALSE;
	    do {
		$rndR = rand(0, 255); $rndG = rand(0, 255); $rndB = rand(0, 255);
		$rndcolor = $rndR + $rndG + $rndB;
		switch ($charcolorrndlevel) {
		    case 1: if ($rndcolor < 200) {$ok = TRUE;} break; // very dark
		    case 2: if ($rndcolor < 400) {$ok = TRUE;} break; // dark
		    case 3: if ($rndcolor > 500) {$ok = TRUE;} break; // light
		    case 4: if ($rndcolor > 650) {$ok = TRUE;} break; // very light
		    default : $ok = TRUE;
		}
	    } while (!$ok);

	    if (function_exists ('imagecolorallocatealpha')) {
		$rndink = imagecolorallocatealpha($img, $rndR, $rndG, $rndB, $charclear);
	    } else {
		$rndink = imagecolorallocate ($img, $rndR, $rndG, $rndB);
	    }
	}

	$lafont = 'fonts/'.$tword[$i]['font'];
	imagettftext($img, $tword[$i]['size'], $tword[$i]['angle'], $x, $tword[$i]['y'],
		$charcolorrnd ? $rndink : $ink, $lafont, $tword[$i]['element']);

	$x += $charspace;
    }
}


// Fonction permettant de déterminer la couleur du bruit et la forme du pinceau
function noisecolor() {
    global $img, $noisecolorchar, $ink, $bg, $brushsize;

    switch ($noisecolorchar) {
	case 1: $noisecol = $ink; break;
	case 2: $noisecol = $bg; break;
	case 3:
	default: $noisecol = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    }
    return $noisecol;
}


// Ajout de bruits: point, lignes et cercles aléatoires
function bruit() {
    global $noisepxmin, $noisepxmax, $noiselinemin, $noiselinemax, $nbcirclemin,
	    $nbcirclemax, $img, $cryptwidth, $cryptheight, $brushsize;

    $nbpx = rand($noisepxmin, $noisepxmax);
    $nbline = rand($noiselinemin, $noiselinemax);
    $nbcircle = rand($nbcirclemin, $nbcirclemax);
    for ($i=1; $i < $nbpx; $i++) {
	imagesetpixel($img, rand(0, $cryptwidth - 1), rand(0, $cryptheight - 1), noisecolor());
    }
    imagesetthickness($img, $brushsize);
    for ($i=1; $i <= $nbline; $i++) {
	imageline($img, rand(0, $cryptwidth - 1), rand(0, $cryptheight - 1), rand(0, $cryptwidth - 1), rand(0, $cryptheight - 1), noisecolor());
    }
    for ($i=1; $i <= $nbcircle; $i++) {
	imagearc($img, rand(0, $cryptwidth - 1), rand(0, $cryptheight - 1),
		$rayon = rand(5, $cryptwidth / 3), $rayon, 0, 359, noisecolor());
    }
}


if ($noiseup) {
   ecriture();
   bruit();
} else {
    bruit();
    ecriture();
}


// Création du cadre
if ($bgframe) {
   $framecol = imagecolorallocate($img, ($bgR * 3 + $charR) / 4, ($bgG * 3 + $charG) / 4, ($bgB * 3 + $charB) / 4);
   imagerectangle($img, 0, 0, $cryptwidth - 1, $cryptheight - 1, $framecol);
}


// Transformations supplémentaires: Grayscale et Brouillage
// Vérifie si la fonction existe dans la version PHP installée
if (function_exists('imagefilter')) {
   if ($cryptgrayscal) {imagefilter($img, IMG_FILTER_GRAYSCALE);}
   if ($cryptgaussianblur) {imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);}
}


$_SESSION['cryptographp_code'][$id] = $word;
$_SESSION['cryptographp_time'][$id] = time();


// Send the final image to the browser
switch (strtoupper($cryptformat)) {
    case "JPG":
    case "JPEG":
	if (imagetypes() & IMG_JPG) {
	    header("Content-type: image/jpeg");
	    imagejpeg($img, '', 80);
	}
	break;
    case "GIF":
	if (imagetypes() & IMG_GIF) {
	    header("Content-type: image/gif");
	    imagegif($img);
	}
	break;
    default:
	if (imagetypes() & IMG_PNG) {
	    header("Content-type: image/png");
	    imagepng($img);
	}
}
imagedestroy($img);

?>
