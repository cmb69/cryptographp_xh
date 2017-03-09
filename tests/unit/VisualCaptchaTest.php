<?php

/**
 * Copyright 2017 Christoph M. Becker
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

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;

class VisualCaptchaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var VisualCaptcha
     */
    private $subject;

    public function setUp()
    {
        $this->setUpFilesystem();
        $this->setUpConfig();
        $this->subject = new VisualCaptcha;
    }

    private function setUpFilesystem()
    {
        global $pth;

        vfsStream::setup('test');
        $pth['folder'] = array(
            'images' => vfsStream::url('test/images/'),
            'plugins' => '../'
        );
        mkdir(vfsStream::url('test/images'), 0777, true);
    }

    private function setUpConfig()
    {
        global $plugin_cf;

        $plugin_cf['cryptographp'] = array(
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
        );
    }

    public function testCreateImage()
    {
        $actual = $this->subject->createImage('ABCD');
        $this->assertSame('7cb5f96a68163626578bf100d1dc9768', $this->calculateImageHash($actual));
    }

    public function testCreateErrorImage()
    {
        $actual = $this->subject->createErrorImage('Cookies must be enabled!');
        $this->assertSame('3bf1a81ebd43fd93f59429a18ccf65af', $this->calculateImageHash($actual));
    }

    public function testNoiseAbove()
    {
        global $plugin_cf;

        $plugin_cf['cryptographp']['noise_above'] = 'true';
        $subject = new VisualCaptcha;
        $actual = $subject->createImage('ABCD');
        $this->assertSame('7cb5f96a68163626578bf100d1dc9768', $this->calculateImageHash($actual));
    }

    public function testCryptGrayScale()
    {
        global $plugin_cf;

        $plugin_cf['cryptographp']['crypt_gray_scale'] = 'true';
        $subject = new VisualCaptcha;
        $actual = $subject->createImage('ABCD');
        $this->assertSame('66f29f71ee6d6b30c08459936e9294d8', $this->calculateImageHash($actual));
    }

    public function testCryptGaussianBlur()
    {
        global $plugin_cf;

        $plugin_cf['cryptographp']['crypt_gaussian_blur'] = 'true';
        $subject = new VisualCaptcha;
        $actual = $subject->createImage('ABCD');
        $this->assertSame('421220c58b7f94f3d9296ebae6fb4ec7', $this->calculateImageHash($actual));
    }

    /**
     * @dataProvider provideBgImageData
     * @param string $type
     */
    public function testBgImage($type)
    {
        global $plugin_cf;

        $plugin_cf['cryptographp']['bg_image'] = "bg.$type";
        $this->createYellowBackgroundImage($type);
        $subject = new VisualCaptcha;
        $actual = $subject->createImage('ABCD');
        $this->assertSame('8698d52fa3ca77b981d5fb0cfc389518', $this->calculateImageHash($actual));
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
        global $plugin_cf;

        $plugin_cf['cryptographp']['bg_image'] = '.';
        $this->createYellowBackgroundImage('png');
        $this->createYellowBackgroundImage('gif');
        $subject = new VisualCaptcha;
        $actual = $subject->createImage('ABCD');
        $this->assertSame('8698d52fa3ca77b981d5fb0cfc389518', $this->calculateImageHash($actual));
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
        global $plugin_cf;

        $plugin_cf['cryptographp']['noise_color'] = $kind;
        $subject = new VisualCaptcha;
        $actual = $subject->createImage('ABCD');
        $this->assertSame($expected, $this->calculateImageHash($actual));
    }

    /**
     * @return array
     */
    public function provideNoiseColorData()
    {
        return array(
            ['1', '81076c5f1afef7c8b471a7e499fdd6cd'],
            ['2', '2899705886d9c70f85bceecfd03f2320']
        );
    }

    private function calculateImageHash($image)
    {
        ob_start();
        imagepng($image);
        $hash = md5(ob_get_clean());
        //imagepng($image, "$hash.png"); // for visual inspection
        return $hash;
    }
}
