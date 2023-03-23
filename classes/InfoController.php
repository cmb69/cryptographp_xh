<?php

/**
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2023 Christoph M. Becker
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

use Cryptographp\Infra\SystemChecker;
use Cryptographp\Infra\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var SystemChecker */
    private $systemChecker;

    /** @param array<string,string> $lang */
    public function __construct(string $pluginFolder, array $lang, SystemChecker $systemChecker)
    {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->systemChecker = $systemChecker;
    }

    public function __invoke(): string
    {
        $view = new View("{$this->pluginFolder}views", $this->lang);
        return $view->render('info', [
            'version' => CRYPTOGRAPHP_VERSION,
            'checks' => $this->getChecks(),
        ]);
    }

    /** @return array<array{state:string,label:string,stateLabel:string}> */
    private function getChecks()
    {
        return array(
            $this->checkPhpVersion('7.0.0'),
            $this->checkExtension('gd'),
            $this->checkGdFreetype(),
            $this->checkGdPng(),
            $this->checkXhVersion('1.7.0'),
            $this->checkWritability("{$this->pluginFolder}config/"),
            $this->checkWritability("{$this->pluginFolder}css/"),
            $this->checkWritability("{$this->pluginFolder}languages/")
        );
    }

    /** @return array{state:string,label:string,stateLabel:string} */
    private function checkPhpVersion(string $version)
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_phpversion'], $version);
        $stateLabel = $this->lang["syscheck_$state"];
        return compact('state', 'label', 'stateLabel');
    }

    /** @return array{state:string,label:string,stateLabel:string} */
    private function checkExtension(string $extension)
    {
        $state = $this->systemChecker->checkExtension($extension) ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_extension'], $extension);
        $stateLabel = $this->lang["syscheck_$state"];
        return compact('state', 'label', 'stateLabel');
    }

    /** @return array{state:string,label:string,stateLabel:string} */
    private function checkGdFreetype()
    {
        $state = $this->systemChecker->checkGdFreetype() ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_gd_feature'], 'TrueType');
        $stateLabel = $this->lang["syscheck_$state"];
        return compact('state', 'label', 'stateLabel');
    }

    /** @return array{state:string,label:string,stateLabel:string} */
    private function checkGdPng()
    {
        $state = $this->systemChecker->checkGdPng() ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_gd_feature'], 'PNG');
        $stateLabel = $this->lang["syscheck_$state"];
        return compact('state', 'label', 'stateLabel');
    }

    /** @return array{state:string,label:string,stateLabel:string} */
    private function checkXhVersion(string $version)
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version")
            ? 'success'
            : 'fail';
        $label = sprintf($this->lang['syscheck_xhversion'], $version);
        $stateLabel = $this->lang["syscheck_$state"];
        return compact('state', 'label', 'stateLabel');
    }

    /** @return array{state:string,label:string,stateLabel:string} */
    private function checkWritability(string $folder)
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        $label = sprintf($this->lang['syscheck_writable'], $folder);
        $stateLabel = $this->lang["syscheck_$state"];
        return compact('state', 'label', 'stateLabel');
    }
}
