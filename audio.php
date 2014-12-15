<?php

/**
 * Front-end of Cryptographp_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Crpytographp
 * @author    Sylvain Brison <cryptographp@alphpa.com>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */

if (session_id() == '') {
    session_start();
}
$id = $_GET['id'];
$lang = basename($_GET['lang']);

if (!isset($_SESSION['cryptographp_code'][$id])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$code = $_SESSION['cryptographp_code'][$id];

$o = '';
for ($i = 0; $i < strlen($code); $i++) {
    $cnt = file_get_contents(
        './languages/' . $lang . '/' . strtolower($code[$i]) . '.mp3'
    );
    if ($cnt !== false) {
        $o .= $cnt;
    } else {
        /**
         * The default localization.
         */
        include './languages/default.php';
        /**
         * The localization of the current language.
         */
        include './languages/' . $lang . '.php';
        exit($plugin_tx['cryptographp']['error_audio']);
    }
}

header('Content-Type: audio/mpeg');
if (isset($_GET['download'])) {
    header('Content-Disposition: attachment; filename=captcha.mp3');
}
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . strlen($o));
echo $o;

?>
