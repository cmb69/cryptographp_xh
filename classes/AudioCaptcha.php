<?php

/**
 * The audio CAPTCHAs.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Cryptographp
 * @author    Sylvain Brison <cryptographp@alphpa.com>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */

namespace Cryptographp;

/**
 * The audio CAPTCHAs.
 *
 * @category CMSimple_XH
 * @package  Cryptographp
 * @author   Sylvain Brison <cryptographp@alphpa.com>
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */
class AudioCaptcha
{
    /**
     * Delivers the audio CAPTCHA to the client.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    public function deliver()
    {
        global $pth;

        $id = $_GET['cryptographp_id'];
        $lang = basename($_GET['cryptographp_lang']);
        if (!is_dir($pth['folder']['plugins'] . 'cryptographp/languages/' . $lang)) {
            $lang = 'en';
        }
        if (session_id() == '') {
            session_start();
        }
        if (!isset($_SESSION['cryptographp_code'][$id])) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        $o = $this->makeAudio($id, $lang);
        header('Content-Type: audio/mpeg');
        if (isset($_GET['cryptographp_download'])) {
            header('Content-Disposition: attachment; filename="captcha.mp3"');
        }
        header('Content-Length: ' . strlen($o));
        echo $o;
    }

    /**
     * Creates and returns an audio CAPTCHA.
     *
     * @param string $id   A CAPTCHA ID.
     * @param string $lang A language code.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    protected function makeAudio($id, $lang)
    {
        global $pth, $plugin_tx;

        $code = $_SESSION['cryptographp_code'][$id];
        $o = '';
        for ($i = 0; $i < strlen($code); $i++) {
            $cnt = file_get_contents(
                $pth['folder']['plugins'] . 'cryptographp/languages/'
                . $lang . '/' . strtolower($code[$i]) . '.mp3'
            );
            if ($cnt !== false) {
                $o .= $cnt;
            } else {
                exit($plugin_tx['cryptographp']['error_audio']);
            }
        }
        return $o;
    }
}

?>
