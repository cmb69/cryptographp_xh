<?php

/**
 * Copyright 2006-2007 Sylvain Brison
 * Copyright 2011-2017 Christoph M. Becker
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

class CaptchaController
{
    /**
     * @var bool
     */
    private static $isJavaScriptEmitted = false;

    /**
     * @var string
     */
    private $pluginFolder;

    public function __construct()
    {
        global $pth;

        $this->pluginFolder = "{$pth['folder']['plugins']}cryptographp/";
    }

    public function defaultAction()
    {
        global $sn, $sl;

        if (session_id() == '') {
            session_start();
        }
        $_SESSION['cryptographp_id'] = isset($_SESSION['cryptographp_id']) ? $_SESSION['cryptographp_id'] + 1 : 1;
        $this->emitJavaScript();

        $view = new View('captcha');
        $view->id = $_SESSION['cryptographp_id'];
        $view->imageUrl = "$sn?cryptographp_mode=video&cryptographp_id={$view->id}";

        $get = "cryptographp_mode=audio&cryptographp_id={$view->id}&cryptographp_lang=$sl";
        $view->audioUrl = "$sn?$get&cryptographp_download=yes";
        $view->audioImage = "{$this->pluginFolder}images/audio.png";
        
        $view->reloadUrl = "$sn?cryptographp_mode=video&cryptographp_id={$view->id}";
        $view->reloadImage = "{$this->pluginFolder}images/reload.png";
        $view->render();
    }

    private function emitJavaScript()
    {
        global $bjs;

        if (!self::$isJavaScriptEmitted) {
            $bjs .= "<script type=\"text/javascript\" src=\"{$this->pluginFolder}cryptographp.js\"></script>";
            self::$isJavaScriptEmitted = true;
        }
    }
}
