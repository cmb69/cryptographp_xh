<?php

/**
 * Copyright 2017-2021 Christoph M. Becker
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

namespace Cryptographp\Model;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class VisualCaptchaTest extends TestCase
{
    public function setUp(): void
    {
        vfsStream::setup("root");
        mkdir("vfs://root/images/", 0777, true);
    }

    public function testCreateImage()
    {
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $this->conf());
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals('image', $actual);
    }

    // public function testCreateErrorImage()
    // {
    //     $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $this->conf());
    //     $actual = $sut->createErrorImage('Cookies must be enabled!');
    //     $this->assertImageEquals('error_image', $actual);
    // }

    public function testNoiseAbove()
    {
        $config = ['noise_above' => 'true'] + $this->conf();
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $config);
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals('noise_above', $actual);
    }

    public function testCryptGrayScale()
    {
        $config = ['crypt_gray_scale' => 'true'] + $this->conf();
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $config);
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals('gray_scale', $actual);
    }

    public function testCryptGaussianBlur()
    {
        $config = ['crypt_gaussian_blur' => 'true'] + $this->conf();
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $config);
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals('gaussian_blur', $actual);
    }

    /** @dataProvider bgImages */
    public function testBgImage(string $type)
    {
        $config = ['bg_image' => "bg.$type"] + $this->conf();
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $config);
        $this->createYellowBackgroundImage($type);
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals("bg_image", $actual);
    }

    public function bgImages(): array
    {
        return [
            ['png'],
            ['gif'],
            ['jpeg']
        ];
    }

    public function testBgImages()
    {
        $config = ['bg_image' => "."] + $this->conf();
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $config);
        $this->createYellowBackgroundImage('png');
        $this->createYellowBackgroundImage('gif');
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals('bg_images', $actual);
    }

    private function createYellowBackgroundImage(string $type)
    {
        $backgroundImage = imagecreate(130, 40);
        imagecolorallocate($backgroundImage, 255, 255, 0);
        $writeImage = "image$type";
        $writeImage($backgroundImage, "vfs://root/images/bg.$type");
    }

    /** @dataProvider noiseColors */
    public function testNoiseColor(string $kind, string $expected)
    {
        $config = ['noise_color' => $kind] + $this->conf();
        $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', $config);
        $actual = $sut->createImage('ABCD');
        $this->assertImageEquals($expected, $actual);
    }

    public function noiseColors(): array
    {
        return [
            // ['1', 'noise_color_1'], // fix underlying bug
            ['2', 'noise_color_2']
        ];
    }

    // /**
    //  * @link https://github.com/cmb69/cryptographp_xh/issues/5
    //  */
    // public function testWordwrapInErrorImage()
    // {
    //     $sut = new FakeVisualCaptcha("vfs://root/images/", '../cryptographp/fonts', self::CONFIG);
    //     $actual = $sut->createErrorImage('Перезагрузка слишком быстро!');
    //     $this->assertImageEquals('word_wrap_in_error', $actual);
    // }

    private function conf(): array
    {
        return XH_includeVar("./config/config.php", "plugin_cf")["cryptographp"];
    }

    /**
     * @param string $data
     * @return void
     */
    private function assertImageEquals(string $expected, $data)
    {
        $im1 = imagecreatefrompng(__DIR__ . "/../images/$expected.png");
        $im2 = imagecreatefromstring($data);

        $w1 = imagesx($im1);
        $h1 = imagesy($im1);

        $w2 = imagesx($im2);
        $h2 = imagesy($im2);

        $this->assertEquals($w1, $w2);
        $this->assertEquals($h1, $h2);

        $im3 = imagecreatetruecolor($w1, $h1);
        imagealphablending($im3, false);

        $difference = 0.0;
        for ($i = 0; $i < $w1; $i++) {
            for ($j = 0; $j < $h1; $j++) {
                $c1 = imagecolorat($im1, $i, $j);
                $c2 = imagecolorat($im2, $i, $j);

                $a1 = ($c1 >> 24) & 0x7f;
                $r1 = ($c1 >> 16) & 0xff;
                $g1 = ($c1 >>  8) & 0xff;
                $b1 = ($c1 >>  0) & 0xff;

                $a2 = ($c2 >> 24) & 0x7f;
                $r2 = ($c2 >> 16) & 0xff;
                $g2 = ($c2 >>  8) & 0xff;
                $b2 = ($c2 >>  0) & 0xff;

                if ($a1 !== 0 || $a2 !== 0) {
                    $this->assertTrue(false);
                }

                $d = sqrt(($r1 - $r2)**2 + ($g1 - $g2)**2 + ($b1 - $b2)**2) / sqrt(3 * 255**2);
                $difference += $d;
            }
        }

        if ($difference > 0.0) {
            imagesavealpha($im2, true);
            imagepng($im2, __DIR__ . "/../images/$expected.out.png");
        }

        return $this->assertEqualsWithDelta(0.0, $difference / ($w1 * $h1), 0.02);
    }
}
