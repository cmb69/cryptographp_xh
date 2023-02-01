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

namespace Cryptographp;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class VisualCaptchaTest extends TestCase
{
    const CONFIG = [
        'bg_clear' => 'true',
        'bg_frame' => 'true',
        'bg_image' => '',
        'bg_rgb_blue' => '255',
        'bg_rgb_green' => '255',
        'bg_rgb_red' => '255',
        'char_angle_max' => '25',
        'char_clear' => '0',
        'char_color_random' => 'true',
        'char_color_random_level' => '2',
        'char_displace' => 'true',
        'char_fonts' => 'luggerbu.ttf',
        'char_rgb_blue' => '0',
        'char_rgb_green' => '0',
        'char_rgb_red' => '0',
        'char_size_max' => '16',
        'char_size_min' => '14',
        'char_space' => '20',
        'crypt_gaussian_blur' => '',
        'crypt_gray_scale' => '',
        'crypt_width' => '130',
        'crypt_height' => '40',
        'noise_above' => '',
        'noise_brush_size' => '3',
        'noise_circle_max' => '1',
        'noise_circle_min' => '1',
        'noise_color' => '3',
        'noise_line_max' => '1',
        'noise_line_min' => '1',
        'noise_pixel_max' => '500',
        'noise_pixel_min' => '500'
    ];

    /**
     * @var VisualCaptcha
     */
    private $subject;

    public function setUp(): void
    {
        $this->setUpFilesystem();
        mt_srand(12345);
        $this->subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', self::CONFIG);
    }

    private function setUpFilesystem()
    {
        vfsStream::setup('test');
        mkdir(vfsStream::url('test/images'), 0777, true);
    }

    public function testCreateImage()
    {
        $this->markTestSkipped('fails in CI');
        $actual = $this->subject->createImage('ABCD');
        $this->assertImageEquals('image', $actual);
    }

    public function testCreateErrorImage()
    {
        $this->markTestSkipped('fails in CI');
        $this->markTestSkipped('fails in CI');
        $actual = $this->subject->createErrorImage('Cookies must be enabled!');
        $this->assertImageEquals('error_image', $actual);
    }

    public function testNoiseAbove()
    {
        $config = array_merge(self::CONFIG, ['noise_above' => 'true']);
        $subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', $config);
        $actual = $subject->createImage('ABCD');
        $this->assertImageEquals('noise_above', $actual);
    }

    public function testCryptGrayScale()
    {
        $this->markTestSkipped('fails in CI');
        $config = array_merge(self::CONFIG, ['crypt_gray_scale' => 'true']);
        $subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', $config);
        $actual = $subject->createImage('ABCD');
        $this->assertImageEquals('gray_scale', $actual);
    }

    public function testCryptGaussianBlur()
    {
        $this->markTestSkipped('fails in CI');
        $config = array_merge(self::CONFIG, ['crypt_gaussian_blur' => 'true']);
        $subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', $config);
        $actual = $subject->createImage('ABCD');
        $this->assertImageEquals('gaussian_blur', $actual);
    }

    /**
     * @dataProvider provideBgImageData
     * @param string $type
     */
    public function testBgImage($type)
    {
        $this->markTestSkipped('fails in CI');
        $config = array_merge(self::CONFIG, ['bg_image' => "bg.$type"]);
        $subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', $config);
        $this->createYellowBackgroundImage($type);
        $actual = $subject->createImage('ABCD');
        $this->assertImageEquals('bg_image', $actual);
    }

    /**
     * @return array
     */
    public function provideBgImageData()
    {
        return array(
            ['png'],
            ['gif'],
            ['jpeg']
        );
    }

    public function testBgImages()
    {
        $this->markTestSkipped('fails in CI');
        $config = array_merge(self::CONFIG, ['bg_image' => "."]);
        $subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', $config);
        $this->createYellowBackgroundImage('png');
        $this->createYellowBackgroundImage('gif');
        $actual = $subject->createImage('ABCD');
        $this->assertImageEquals('bg_images', $actual);
    }

    /**
     * @param string $type
     */
    private function createYellowBackgroundImage($type)
    {
        $backgroundImage = imagecreate(130, 40);
        imagecolorallocate($backgroundImage, 255, 255, 0);
        $writeImage = "image$type";
        $writeImage($backgroundImage, vfsStream::url("test/images/bg.$type"));
    }

    /**
     * @dataProvider provideNoiseColorData
     * @param string $kind
     * @param string $expected
     */
    public function testNoiseColor($kind, $expected)
    {
        $this->markTestSkipped('fails in CI');
        $config = array_merge(self::CONFIG, ['noise_color' => $kind]);
        $subject = new VisualCaptcha(vfsStream::url('test/images/'), '../cryptographp/fonts', $config);
        $actual = $subject->createImage('ABCD');
        $this->assertImageEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function provideNoiseColorData()
    {
        return array(
            ['1', 'noise_color_1'],
            ['2', 'noise_color_2']
        );
    }

    /**
     * @link https://github.com/cmb69/cryptographp_xh/issues/5
     */
    public function testWordwrapInErrorImage()
    {
        $this->markTestSkipped('fails in CI');
        $actual = $this->subject->createErrorImage('Перезагрузка слишком быстро!');
        $this->assertImageEquals('word_wrap_in_error', $actual);
    }

    /**
     * @param string $expected
     * @param resource $actual
     * @return void
     */
    private function assertImageEquals($expected, $actual)
    {
        $im1 = imagecreatefrompng(__DIR__ . "/images/$expected.png");
        $im2 = $actual;

        $w1 = imagesx($im1);
        $h1 = imagesy($im1);

        $w2 = imagesx($im2);
        $h2 = imagesy($im2);

        if ($w1 !== $w2 || $h1 !== $h2) {
            $this->assertTrue(false);
        }

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

                $a3 = 127 - (int) (127 * $d);
                $r3 = (int) (sqrt($r1**2 + $r2**2) / sqrt(2 * 255**2) * 255);
                $g3 = (int) (sqrt($g1**2 + $g2**2) / sqrt(2 * 255**2) * 255);
                $b3 = (int) (sqrt($b1**2 + $b2**2) / sqrt(2 * 255**2) * 255);
        
                $c3 = ($a3 << 24) | ($r3 << 16) | ($g3 << 8) | $b3;
        
                imagesetpixel($im3, $i, $j, $c3);
            }
        }

        if ($difference > 0.0) {
            imagesavealpha($im2, true);
            imagepng($im2, __DIR__ . "/images/$expected.out.png");
            imagesavealpha($im3, true);
            imagepng($im3, __DIR__ . "/images/$expected.diff.png");
        }

        return $this->assertTrue($difference === 0.0);
    }
}
