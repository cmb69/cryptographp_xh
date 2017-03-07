<?php

/**
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

namespace Cryptographp;

class AudioCaptcha
{
    /**
     * @return void
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
     * @param string $id
     * @param string $lang
     * @return string
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
