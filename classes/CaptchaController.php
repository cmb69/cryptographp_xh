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
use Cryptographp\Infra\VisualCaptcha;
use Cryptographp\Logic\Util;
use Cryptographp\Value\Response;
use Exception;
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
        switch ($request->action()) {
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
        $this->codeStore->put($key, $code);
        $url = $request->url();
        $nonce = Util::encodeBase64url($key);
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
            $image = $this->visualCaptcha->createErrorImage($this->view->plain("error_video"));
            if ($image === null) {
                throw new Exception("ugh, what now?");
            }
            return Response::create($image)->withContentType("image/png");
        }
        $code = $this->codeStore->find(Util::decodeBase64url($nonce));
        if ($code === null) {
            $image = $this->visualCaptcha->createErrorImage($this->view->plain("error_video"));
            if ($image === null) {
                throw new Exception("ugh, what now?");
            }
            return Response::create($image)->withContentType("image/png");
        }
        $image = $this->visualCaptcha->createImage($code);
        if ($image === null) {
            $image = $this->visualCaptcha->createErrorImage($this->view->plain("error_video"));
            if ($image === null) {
                throw new Exception("ugh, what now?");
            }
            return Response::create($image)->withContentType("image/png");
        }
        return Response::create($image)->withContentType("image/png");
    }

    private function audioAction(Request $request): Response
    {
        $nonce = $request->get("cryptographp_nonce");
        if ($nonce === null || strlen($nonce) % 4 !== 0) {
            return Response::forbid();
        }
        $code = $this->codeStore->find(Util::decodeBase64url($nonce));
        if ($code === null) {
            return Response::forbid($this->view->plain("error_audio"));
        }
        $wav = $this->audioCaptcha->createWav($request->sl(), $code);
        if (!isset($wav)) {
            return Response::forbid($this->view->plain("error_audio"));
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
        $nonce = Util::decodeBase64url($nonce);
        $storedCode = $this->codeStore->find($nonce);
        if ($storedCode === null || !hash_equals($storedCode, $code)) {
            return false;
        }
        $this->codeStore->invalidate($nonce);
        return true;
    }
}
