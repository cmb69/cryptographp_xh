<?php

/**
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Cryptographp_XH.
 *
 * Cryptographp_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Cryptographp_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Cryptographp_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Cryptographp;

class CaptchaController
{
    /**
     * @var bool
     */
    private static $isJavaScriptEmitted = false;

    /**
     * @var string
     */
    private $pluginFolder;

    /**
     * @var string
     */
    private $scriptName;

    /**
     * @var string
     */
    private $currentLang;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $lang;

    public function __construct()
    {
        global $pth, $sn, $sl, $plugin_cf, $plugin_tx;

        $this->pluginFolder = "{$pth['folder']['plugins']}cryptographp/";
        $this->scriptName = $sn;
        $this->currentLang = $sl;
        $this->config = $plugin_cf['cryptographp'];
        $this->lang = $plugin_tx['cryptographp'];
        if (session_id() == '') {
            session_start();
        }
    }

    public function defaultAction()
    {
        $_SESSION['cryptographp_id'] = isset($_SESSION['cryptographp_id']) ? $_SESSION['cryptographp_id'] + 1 : 1;
        $this->emitJavaScript();
        $view = new View('captcha');
        $view->id = $_SESSION['cryptographp_id'];
        $url = new Url($this->scriptName, $_GET);
        $view->imageUrl = $url->with('cryptographp_action', 'video')->with('cryptographp_id', $view->id);
        $view->audioUrl = $url->with('cryptographp_action', 'audio')->with('cryptographp_id', $view->id)
            ->with('cryptographp_lang', $this->currentLang)->with('cryptographp_download', 'yes');
        $view->audioImage = "{$this->pluginFolder}images/audio.png";
        $view->reloadUrl = $view->imageUrl;
        $view->reloadImage = "{$this->pluginFolder}images/reload.png";
        $view->render();
    }

    private function emitJavaScript()
    {
        global $bjs;

        if (!self::$isJavaScriptEmitted) {
            $bjs .= "<script type=\"text/javascript\" src=\"{$this->pluginFolder}cryptographp.js\"></script>";
            self::$isJavaScriptEmitted = true;
        }
    }

    public function videoAction()
    {
        $captcha = new VisualCaptcha();

        if (!isset($_SESSION['cryptographp_id'])) {
            $this->deliverImage($captcha->createErrorImage($this->lang['error_cookies']));
        }
        $id = $_GET['cryptographp_id'];
        $delay = time() - $_SESSION['cryptographp_time'][$id];
        if ($delay < $this->config['crypt_use_timer']) {
            if ($this->config['crypt_use_timer_error']) {
                $this->deliverImage($captcha->createErrorImage($this->lang['error_user_time']));
            } else {
                sleep($this->config['crypt_use_timer'] - $delay);
            }
        }
        $codeGenerator = new CodeGenerator;
        $code = $codeGenerator->createCode();
        $image = $captcha->createImage($code);
        $_SESSION['cryptographp_code'][$id] = $code;
        $_SESSION['cryptographp_time'][$id] = time();
        $this->deliverImage($image);
    }

    private function deliverImage($image)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        switch (strtoupper($this->config['crypt_format'])) {
            case 'JPG':
            case 'JPEG':
                if (imagetypes() & IMG_JPG) {
                    header('Content-type: image/jpeg');
                    imagejpeg($image, '', 80);
                }
                break;
            case 'GIF':
                if (imagetypes() & IMG_GIF) {
                    header('Content-type: image/gif');
                    imagegif($image);
                }
                break;
            default:
                if (imagetypes() & IMG_PNG) {
                    header('Content-type: image/png');
                    imagepng($image);
                }
        }
        exit;
    }

    public function audioAction()
    {
        $id = $_GET['cryptographp_id'];
        $lang = basename($_GET['cryptographp_lang']);
        if (!is_dir("{$this->pluginFolder}languages/$lang")) {
            $lang = 'en';
        }
        if (!isset($_SESSION['cryptographp_code'][$id])) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        $captcha = new AudioCaptcha($lang);
        $mp3 = $captcha->createMp3($_SESSION['cryptographp_code'][$id]);
        if (!isset($mp3)) {
            exit($this->lang['error_audio']);
        }
        header('Content-Type: audio/mpeg');
        if (isset($_GET['cryptographp_download'])) {
            header('Content-Disposition: attachment; filename="captcha.mp3"');
        }
        header('Content-Length: ' . strlen($mp3));
        echo $mp3;
        exit;
    }
}
