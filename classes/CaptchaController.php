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

use GdImage;

class CaptchaController
{
    /**
     * @var bool
     */
    private $isJavaScriptEmitted = false;

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
    private $lang;

    /** @var CodeStore */
    private $codeStore;

    /** @var CodeGenerator */
    private $codeGenerator;

    /** @var VisualCaptcha */
    private $visualCaptcha;

    /** @var AudioCaptcha */
    private $audioCaptcha;

    /** @var View */
    private $view;

    /**
     * @param string $pluginFolder
     * @param string $currentLang
     * @param array<string,string> $lang
     */
    public function __construct(
        $pluginFolder,
        $currentLang,
        array $lang,
        CodeStore $codeStore,
        CodeGenerator $codeGenerator,
        VisualCaptcha $visualCaptcha,
        AudioCaptcha $audioCaptcha,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->currentLang = $currentLang;
        $this->lang = $lang;
        $this->codeStore = $codeStore;
        $this->codeGenerator = $codeGenerator;
        $this->visualCaptcha = $visualCaptcha;
        $this->audioCaptcha = $audioCaptcha;
        $this->view = $view;
    }

    /**
     * @return void
     */
    public function defaultAction()
    {
        $code = $this->codeGenerator->createCode();
        $key = random_bytes(15);
        $this->codeStore->put($key, $code);
        $this->emitJavaScript();
        $url = Url::current();
        $nonce = rtrim(base64_encode($key), "=");
        echo $this->view->render('captcha', [
            'imageUrl' => $url->with('cryptographp_action', 'video')->with('cryptographp_nonce', $nonce),
            'audioUrl' => $url->with('cryptographp_action', 'audio')->with('cryptographp_nonce', $nonce)
                ->with('cryptographp_lang', $this->currentLang)->with('cryptographp_download', 'yes'),
            'audioImage' => "{$this->pluginFolder}images/audio.png",
            'reloadImage' => "{$this->pluginFolder}images/reload.png",
            'nonce' => $nonce,
        ]);
    }

    /**
     * @return void
     */
    private function emitJavaScript()
    {
        global $bjs;

        if (!$this->isJavaScriptEmitted) {
            $bjs .= sprintf(
                '<script type="text/javascript" src="%s"></script>',
                "{$this->pluginFolder}cryptographp.min.js"
            );
            $this->isJavaScriptEmitted = true;
        }
    }

    /**
     * @return void
     */
    public function videoAction()
    {
        if (!isset($_GET['cryptographp_nonce'])) {
            $this->deliverImage($this->visualCaptcha->createErrorImage($this->lang['error_video']));
        }
        $code = $this->codeStore->find(base64_decode($_GET['cryptographp_nonce']));
        $image = $this->visualCaptcha->createImage($code);
        $this->deliverImage($image);
    }

    /**
     * @param resource|GdImage $image
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
        if (!isset($_GET['cryptographp_nonce'])) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        $code = $this->codeStore->find(base64_decode($_GET['cryptographp_nonce']));
        $wav = $this->audioCaptcha->createWav($code);
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
