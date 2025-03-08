<?php

/**
 * Copyright 2023 Christoph M. Becker
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

use ApprovalTests\Approvals;
use Cryptographp\Infra\AudioCaptcha;
use Cryptographp\Infra\CodeGenerator;
use Cryptographp\Infra\CodeStore;
use Cryptographp\Infra\FakeRequest;
use Cryptographp\Infra\VisualCaptcha;
use PHPUnit\Framework\TestCase;
use Plib\View;

class CaptchaControllerTest extends TestCase
{
    public function testRendersCaptcha(): void
    {
        global $su;

        $su = "Page";
        $sut = $this->sut();
        $request = new FakeRequest();
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
    }

    public function testDeliversCaptchaImage(): void
    {
        $sut = $this->sut();
        $_GET = ["cryptographp_action" => "video", "cryptographp_nonce" => "PjPIXZ5y1-8tzTZ_sjHu"];
        $request = new FakeRequest();
        $response = $sut($request);
        $this->assertEquals("some image data", $response->output());
        $this->assertEquals("image/png", $response->contentType());
    }

    public function testDeliversErrorImageOnMissingNonce(): void
    {
        $sut = $this->sut();
        $_GET = ["cryptographp_action" => "video"];
        $request = new FakeRequest();
        $response = $sut($request);
        $this->assertEquals("some error image data", $response->output());
        $this->assertEquals("image/png", $response->contentType());
    }

    public function testDeliversCaptchaAudio(): void
    {
        $sut = $this->sut();
        $_GET = [
            "cryptographp_action" => "audio",
            "cryptographp_nonce" => "PjPIXZ5y1-8tzTZ_sjHu",
            "cryptographp_download" => ""
        ];
        $request = new FakeRequest();
        $response = $sut($request);
        $this->assertEquals("some audio data", $response->output());
        $this->assertEquals("audio/x-wav", $response->contentType());
        $this->assertEquals(15, $response->length());
        $this->assertEquals("captcha.wav", $response->attachment());
    }

    public function testDeniesAccessOnMissingNonce(): void
    {
        $sut = $this->sut();
        $_GET = ["cryptographp_action" => "audio"];
        $request = new FakeRequest();
        $response = $sut($request);
        $this->assertTrue($response->forbidden());
    }

    public function testDeniesAccessOnFailureToCreateWav(): void
    {
        $sut = $this->sut(["createWav" => null]);
        $_GET = ["cryptographp_action" => "audio", "cryptographp_nonce" => "PjPIXZ5y1-8tzTZ_sjHu"];
        $request = new FakeRequest();
        $response = $sut($request);
        $this->assertTrue($response->forbidden());
        $this->assertEquals(
            "The audio CAPTCHA couldn't be generated! Please get a new challenge and try again.",
            $response->output()
        );
    }

    public function testVerifiesCaptcha(): void
    {
        $sut = $this->sut();
        $_POST = [
            "cryptographp-captcha" => "GEVO",
            "cryptographp_nonce" => "PjPIXZ5y1-8tzTZ_sjHu"
        ];
        $result = $sut->verifyCaptcha(new FakeRequest());
        $this->assertTrue($result);
    }

    public function testVerificationsFailsOnMissingNonce(): void
    {
        $sut = $this->sut();
        $result = $sut->verifyCaptcha(new FakeRequest());
        $this->assertFalse($result);
    }

    public function testVerificationsFailsOnWrongCaptcha(): void
    {
        $sut = $this->sut();
        $result = $sut->verifyCaptcha(new FakeRequest());
        $this->assertFalse($result);
    }

    private function sut(array $opts = [])
    {
        $opts += ["createWav" => "some audio data"];
        $text = XH_includeVar("./languages/en.php", "plugin_tx")["cryptographp"];
        $codeStore = $this->createStub(CodeStore::class);
        $codeStore->method("find")->willReturn("GEVO");
        $codeGenerator = $this->createStub(CodeGenerator::class);
        $codeGenerator->method("createCode")->willReturn("GEVO");
        $codeGenerator->method("randomKey")->willReturn(hex2bin("3e33c85d9e72d7ef2dcd367fb231ee"));
        $visualCaptcha = $this->createStub(VisualCaptcha::class);
        $visualCaptcha->method("createImage")->willReturn("some image data");
        $visualCaptcha->method("createErrorImage")->willReturn("some error image data");
        $audioCaptcha = $this->createStub(AudioCaptcha::class);
        $audioCaptcha->method("createWav")->willReturn($opts["createWav"]);
        $view = new View("./views/", $text);
        return new CaptchaController(
            "./plugins/",
            $codeStore,
            $codeGenerator,
            $visualCaptcha,
            $audioCaptcha,
            $view
        );
    }
}
