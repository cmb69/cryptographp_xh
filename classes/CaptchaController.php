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

use Cryptographp\Infra\AudioCaptcha;
use Cryptographp\Infra\CodeGenerator;
use Cryptographp\Infra\CodeStore;
use Cryptographp\Infra\Request;
use Cryptographp\Infra\View;
use Cryptographp\Infra\VisualCaptcha;
use Cryptographp\Value\Response;

class CaptchaController
{
    /** @var bool */
    private $isJavaScriptEmitted = false;

    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
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

    /** @param array<string,string> $lang */
    public function __construct(
        string $pluginFolder,
        array $lang,
        CodeStore $codeStore,
        CodeGenerator $codeGenerator,
        VisualCaptcha $visualCaptcha,
        AudioCaptcha $audioCaptcha,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->codeStore = $codeStore;
        $this->codeGenerator = $codeGenerator;
        $this->visualCaptcha = $visualCaptcha;
        $this->audioCaptcha = $audioCaptcha;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->action()) {
            default:
                return $this->defaultAction($request);
            case "video":
                return $this->videoAction();
            case "audio":
                return $this->audioAction();
        }
    }

    private function defaultAction(Request $request): Response
    {
        $code = $this->codeGenerator->createCode();
        $key = $this->codeGenerator->randomKey();
        $this->codeStore->put($key, $code);
        $this->emitJavaScript();
        $url = $request->url();
        $nonce = rtrim(str_replace(["+", "/"], ["-", "_"], base64_encode($key)), "=");
        return Response::create($this->view->render('captcha', [
            'imageUrl' => $url->with('cryptographp_action', 'video')->with('cryptographp_nonce', $nonce),
            'audioUrl' => $url->with('cryptographp_action', 'audio')->with('cryptographp_nonce', $nonce)
                ->with('cryptographp_lang', $this->lang())->with('cryptographp_download', 'yes'),
            'audioImage' => "{$this->pluginFolder}images/audio.png",
            'reloadImage' => "{$this->pluginFolder}images/reload.png",
            'nonce' => $nonce,
        ]));
    }

    /** @return void */
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

    private function videoAction(): Response
    {
        if (!isset($_GET['cryptographp_nonce'])) {
            return $this->deliverImage($this->visualCaptcha->createErrorImage($this->lang['error_video']));
        }
        $code = $this->codeStore->find(base64_decode(str_replace(["-", "_"], ["+", "/"], $_GET['cryptographp_nonce'])));
        $image = $this->visualCaptcha->createImage($code);
        return $this->deliverImage($image);
    }

    private function deliverImage(string $image): Response
    {
        return Response::create($image)->withContentType("image/png");
    }

    private function audioAction(): Response
    {
        if (!isset($_GET['cryptographp_nonce'])) {
            return Response::forbid();
        }
        $code = $this->codeStore->find(base64_decode(str_replace(["-", "_"], ["+", "/"], $_GET['cryptographp_nonce'])));
        $wav = $this->audioCaptcha->createWav($this->lang(), $code);
        if (!isset($wav)) {
            return Response::forbid($this->lang['error_audio']);
        }
        $response = Response::create($wav)
            ->withContentType("audio/x-wav")
            ->withLength(strlen($wav));
        if (isset($_GET['cryptographp_download'])) {
            $response = $response->withAttachment("captcha.wav");
        }
        return $response;
    }

    private function lang(): string
    {
        $lang = basename($_GET['cryptographp_lang'] ?? "en");
        if (!is_dir($this->pluginFolder . "languages/$lang")) {
            $lang = 'en';
        }
        return $lang;
    }

    public function verifyCaptcha(): bool
    {
        $code = $_POST['cryptographp-captcha'] ?? "";
        if (!isset($_POST['cryptographp_nonce'])) {
            return false;
        }
        $nonce = base64_decode(str_replace(["-", "_"], ["+", "/"], $_POST['cryptographp_nonce']));
        $storedCode = $this->codeStore->find($nonce);
        if ($code !== $storedCode) {
            return false;
        }
        $this->codeStore->invalidate($nonce);
        return true;
    }
}
