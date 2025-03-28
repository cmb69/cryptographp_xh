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
use Cryptographp\Model\AudioCaptcha;
use Cryptographp\Model\CodeGenerator;
use Cryptographp\Model\CodeStore;
use Cryptographp\Model\VisualCaptcha;
use PHPUnit\Framework\TestCase;
use Plib\Request;
use Plib\View;

class CaptchaControllerTest extends TestCase
{
    public function setUp(): void
    {
        global $sl, $su;

        $sl = "en";
        $su = "Page";
    }

    public function testRendersCaptcha(): void
    {
        $sut = $this->sut();
        $request = Request::current();
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
    }

    public function testDeliversCaptchaImage(): void
    {
        $sut = $this->sut();
        $_GET = ["cryptographp_action" => "video", "cryptographp_nonce" => "PjPIXZ5y1-8tzTZ_sjHu"];
        $request = Request::current();
        $response = $sut($request);
        $this->assertEquals("some image data", $response->output());
        $this->assertEquals("image/png", $response->contentType());
    }

    public function testDeliversErrorImageOnMissingNonce(): void
    {
        $sut = $this->sut();
        $_GET = ["cryptographp_action" => "video"];
        $request = Request::current();
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
        $request = Request::current();
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
        $request = Request::current();
        $response = $sut($request);
        $this->assertEquals(403, $response->status());
    }

    public function testReportsInternalServerErrorOnFailureToCreateWav(): void
    {
        $sut = $this->sut(["createWav" => null]);
        $_GET = ["cryptographp_action" => "audio", "cryptographp_nonce" => "PjPIXZ5y1-8tzTZ_sjHu"];
        $request = Request::current();
        $response = $sut($request);
        $this->assertEquals(500, $response->status());
        $this->assertEquals(
            "The audio CAPTCHA couldn't be generated! Please try again.",
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
        $result = $sut->verifyCaptcha(Request::current());
        $this->assertTrue($result);
    }

    public function testVerificationsFailsOnMissingNonce(): void
    {
        $sut = $this->sut();
        $result = $sut->verifyCaptcha(Request::current());
        $this->assertFalse($result);
    }

    public function testVerificationsFailsOnWrongCaptcha(): void
    {
        $sut = $this->sut();
        $result = $sut->verifyCaptcha(Request::current());
        $this->assertFalse($result);
    }

    private function sut(array $opts = [])
    {
        $opts += ["createWav" => "some audio data"];
        $text = XH_includeVar("./languages/en.php", "plugin_tx")["cryptographp"];
        $codeStore = $this->createStub(CodeStore::class);
        $codeStore->method("find")->willReturn("GEVO");
        $codeStore->method("put")->willReturn(true);
        $codeStore->method("invalidate")->willReturn(true);
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
