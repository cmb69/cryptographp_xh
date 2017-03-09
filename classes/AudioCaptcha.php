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

class AudioCaptcha
{
    /**
     * @var string
     */
    private $mp3Folder;

    /**
     * @var string $lang
     */
    public function __construct($lang)
    {
        global $pth;

        $this->mp3Folder = "{$pth['folder']['plugins']}cryptographp/languages/$lang/";
    }

    /**
     * @param string $code
     * @return ?string
     */
    public function createMp3($code)
    {
        $mp3 = '';
        for ($i = 0; $i < strlen($code); $i++) {
            $filename = $this->mp3Folder . strtolower($code[$i]) . '.mp3';
            if (is_readable($filename) && ($contents = file_get_contents($filename))) {
                $mp3 .= $contents;
            } else {
                return null;
            }
        }
        return $mp3;
    }
}
