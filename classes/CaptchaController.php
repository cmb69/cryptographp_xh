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
        if (function_exists('XH_startSession')) {
            XH_startSession();
        } elseif (session_id() == '') {
            session_start();
        }
    }

    public function defaultAction()
    {
        if (!isset($_SESSION['cryptographp_code'])) {
            $code = (new CodeGenerator)->createCode();
            $_SESSION['cryptographp_code'] = $code;
            $_SESSION['cryptographp_time'] = time();
        }
        $this->emitJavaScript();
        $view = new View('captcha');
        $url = new Url($this->scriptName, $_GET);
        $view->imageUrl = $url->with('cryptographp_action', 'video');
        $view->audioUrl = $url->with('cryptographp_action', 'audio')
            ->with('cryptographp_lang', $this->currentLang)->with('cryptographp_download', 'yes');
        $view->audioImage = "{$this->pluginFolder}images/audio.png";
        $view->reloadImage = "{$this->pluginFolder}images/reload.png";
        $view->render();
    }

    private function emitJavaScript()
    {
        global $bjs;

        if (!self::$isJavaScriptEmitted) {
            $bjs .= sprintf(
                '<script type="text/javascript" src="%s"></script>',
                "{$this->pluginFolder}cryptographp.min.js"
            );
            self::$isJavaScriptEmitted = true;
        }
    }

    public function videoAction()
    {
        $captcha = new VisualCaptcha();

        if (!isset($_SESSION['cryptographp_code'])) {
            $this->deliverImage($captcha->createErrorImage($this->lang['error_cookies']));
        }
        $delay = time() - $_SESSION['cryptographp_time'];
        if ($delay < $this->config['crypt_use_timer']) {
            if ($this->config['crypt_use_timer_error']) {
                $this->deliverImage($captcha->createErrorImage($this->lang['error_user_time']));
            } else {
                sleep($this->config['crypt_use_timer'] - $delay);
            }
        }
        $image = $captcha->createImage($_SESSION['cryptographp_code']);
        $this->deliverImage($image);
    }

    private function deliverImage($image)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-type: image/png');
        imagepng($image);
        exit;
    }

    public function audioAction()
    {
        $lang = basename($_GET['cryptographp_lang']);
        if (!is_dir("{$this->pluginFolder}languages/$lang")) {
            $lang = 'en';
        }
        if (!isset($_SESSION['cryptographp_code'])) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        $wav = (new AudioCaptcha($lang))->createWav($_SESSION['cryptographp_code']);
        if (!isset($wav)) {
            exit($this->lang['error_audio']);
        }
        header('Content-Type: audio/x-wav');
        if (isset($_GET['cryptographp_download'])) {
            header('Content-Disposition: attachment; filename="captcha.wav"');
        }
        header('Content-Length: ' . strlen($wav));
        echo $wav;
        exit;
    }
}
