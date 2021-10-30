<?php

/**
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2021 Christoph M. Becker
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
    private $currentLang;

    /**
     * @var array<string,string>
     */
    private $config;

    /**
     * @var array<string,string>
     */
    private $lang;

    /** @var View */
    private $view;

    public function __construct(View $view)
    {
        global $pth, $sl, $plugin_cf, $plugin_tx;

        $this->pluginFolder = "{$pth['folder']['plugins']}cryptographp/";
        $this->currentLang = $sl;
        $this->config = $plugin_cf['cryptographp'];
        $this->lang = $plugin_tx['cryptographp'];
        $this->view = $view;
        XH_startSession();
    }

    /**
     * @return void
     */
    public function defaultAction()
    {
        if (!isset($_SESSION['cryptographp_code'])) {
            $code = (new CodeGenerator)->createCode();
            $_SESSION['cryptographp_code'] = $code;
            $_SESSION['cryptographp_time'] = time();
        }
        $this->emitJavaScript();
        $url = Url::current();
        echo $this->view->render('captcha', [
            'imageUrl' => $url->with('cryptographp_action', 'video'),
            'audioUrl' => $url->with('cryptographp_action', 'audio')
                ->with('cryptographp_lang', $this->currentLang)->with('cryptographp_download', 'yes'),
            'audioImage' => "{$this->pluginFolder}images/audio.png",
            'reloadImage' => "{$this->pluginFolder}images/reload.png",
        ]);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
                sleep((int) $this->config['crypt_use_timer'] - $delay);
            }
        }
        $image = $captcha->createImage($_SESSION['cryptographp_code']);
        $this->deliverImage($image);
    }

    /**
     * @param resource $image
     * @return never
     */
    private function deliverImage($image)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-type: image/png');
        imagepng($image);
        exit;
    }

    /**
     * @return never
     */
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
        while (ob_get_level()) {
            ob_end_clean();
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
