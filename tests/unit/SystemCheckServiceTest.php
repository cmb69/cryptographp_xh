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
use stdClass;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class SystemCheckServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SystemCheckService
     */
    private $subject;

    public function setUp()
    {
        define('CMSIMPLE_XH_VERSION', 'CMSimple_XH 1.6.9');
        $this->setUpLanguage();
        $this->setUpVfs();
        $this->subject = new SystemCheckService;
    }

    private function setUpLanguage()
    {
        global $plugin_tx;

        $plugin_tx['cryptographp'] = array(
            'syscheck_extension' => 'extension',
            'syscheck_gd_feature' => 'gdfeature',
            'syscheck_phpversion' => 'phpversion',
            'syscheck_writable' => 'writable',
            'syscheck_xhversion' => 'xhversion',
            'syscheck_success' => 'success',
            'syscheck_warning' => 'warning',
            'syscheck_fail' => 'fail'
        );
    }

    private function setUpVfs()
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $pth['folder'] = array(
            'base' => vfsStream::url('test/'),
            'plugins' => vfsStream::url('test/plugins/')
        );
        mkdir(vfsStream::url('test/plugins/cryptographp/config'), 0777, true);
    }

    public function testGetChecks()
    {
        $actual = $this->subject->getChecks();
        $this->assertContainsOnlyInstancesOf(stdClass::class, $actual);
        $this->assertCount(9, $actual);
        $this->assertSame('phpversion', $actual[0]->label);
        $this->assertSame('success', $actual[0]->state);
        $this->assertSame('extension', $actual[1]->label);
        $this->assertSame('success', $actual[1]->state);
        $this->assertSame('gdfeature', $actual[2]->label);
        $this->assertSame('success', $actual[2]->state);
        $this->assertSame('xhversion', $actual[5]->label);
        $this->assertSame('success', $actual[5]->state);
        $this->assertSame('writable', $actual[6]->label);
        $this->assertSame('success', $actual[6]->state);
    }
}
