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

use Cryptographp\Model\AudioCaptcha;
use Cryptographp\Model\CodeGenerator;
use Cryptographp\Model\CodeStore;
use Cryptographp\Model\VisualCaptcha;
use Plib\Codec;
use Plib\Request;
use Plib\Response;
use Plib\View;

class CaptchaController
{
    /** @var string */
    private $pluginFolder;

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

    public function __construct(
        string $pluginFolder,
        CodeStore $codeStore,
        CodeGenerator $codeGenerator,
        VisualCaptcha $visualCaptcha,
        AudioCaptcha $audioCaptcha,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->codeStore = $codeStore;
        $this->codeGenerator = $codeGenerator;
        $this->visualCaptcha = $visualCaptcha;
        $this->audioCaptcha = $audioCaptcha;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->get("cryptographp_action") ?? "") {
            default:
                return $this->defaultAction($request);
            case "video":
                return $this->videoAction($request);
            case "audio":
                return $this->audioAction($request);
        }
    }

    private function defaultAction(Request $request): Response
    {
        $code = $this->codeGenerator->createCode();
        $key = $this->codeGenerator->randomKey();
        if (!$this->codeStore->put($key, $code)) {
            return Response::create($this->view->message("fail", "error_captcha"));
        }
        $url = $request->url();
        $nonce = Codec::encodeBase64url($key);
        return Response::create($this->view->render("captcha", [
            "js" => $this->pluginFolder . "cryptographp.min.js",
            "imageUrl" => $url->with("cryptographp_action", "video")->with("cryptographp_nonce", $nonce)->relative(),
            "audioUrl" => $url->with("cryptographp_action", "audio")->with("cryptographp_nonce", $nonce)
                ->with("cryptographp_download", "yes")->relative(),
            "audioImage" => $this->pluginFolder . "images/audio.png",
            "reloadImage" => $this->pluginFolder . "images/reload.png",
            "nonce" => $nonce,
        ]));
    }

    private function videoAction(Request $request): Response
    {
        $nonce = $request->get("cryptographp_nonce");
        if ($nonce === null || strlen($nonce) % 4 !== 0) {
            return $this->errorImage();
        }
        $nonce = Codec::decodeBase64url($nonce);
        if ($nonce === null) {
            return $this->errorImage();
        }
        $code = $this->codeStore->find($nonce);
        if ($code === null) {
            return $this->errorImage();
        }
        $image = $this->visualCaptcha->createImage($code);
        if ($image === null) {
            return $this->errorImage();
        }
        return Response::create($image)->withContentType("image/png");
    }

    private function errorImage(): Response
    {
        $image = $this->visualCaptcha->createErrorImage($this->view->plain("error_video"));
        if ($image === null) {
            return Response::error(500, $this->view->plain("error_video"));
        }
        return Response::create($image)->withContentType("image/png");
    }

    private function audioAction(Request $request): Response
    {
        $nonce = $request->get("cryptographp_nonce");
        if ($nonce === null || strlen($nonce) % 4 !== 0) {
            return Response::error(403);
        }
        $nonce = Codec::decodeBase64url($nonce);
        if ($nonce === null) {
            return Response::error(403);
        }
        $code = $this->codeStore->find($nonce);
        if ($code === null) {
            return Response::error(403, $this->view->plain("error_audio"));
        }
        $wav = $this->audioCaptcha->createWav($request->language(), $code);
        if (!isset($wav)) {
            return Response::error(500, $this->view->plain("error_audio"));
        }
        $response = Response::create($wav)
            ->withContentType("audio/x-wav")
            ->withLength(strlen($wav));
        if ($request->get("cryptographp_download") !== null) {
            $response = $response->withAttachment("captcha.wav");
        }
        return $response;
    }

    public function verifyCaptcha(Request $request): bool
    {
        $code = $request->post("cryptographp-captcha") ?? "";
        $nonce = $request->post("cryptographp_nonce") ?? "";
        if ($nonce === "" || strlen($nonce) % 4 !== 0) {
            return false;
        }
        $nonce = Codec::decodeBase64url($nonce);
        if ($nonce === null) {
            return false;
        }
        $storedCode = $this->codeStore->find($nonce);
        if ($storedCode === null || !hash_equals($storedCode, $code)) {
            return false;
        }
        if (!$this->codeStore->invalidate($nonce)) {
            return false;
        }
        return true;
    }
}
