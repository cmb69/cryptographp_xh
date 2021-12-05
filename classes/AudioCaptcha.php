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

class AudioCaptcha
{
    const NOISE_PEAK = 4000;

    /**
     * @var string
     */
    private $audioFolder;

    /**
     * @param string $audioFolder
     */
    public function __construct($audioFolder)
    {
        $this->audioFolder = $audioFolder;
    }

    /**
     * @param string $code
     * @return string|null
     */
    public function createWav($code)
    {
        if (!($samples = $this->concatenateRawAudio($code))) {
            return null;
        }
        $dataChunk = $this->createDataChunk($this->applyWhiteNoise($samples));
        return $this->createRiffChunk($dataChunk) . $this->createFmtChunk() . $dataChunk;
    }

    /**
     * @param string $dataChunk
     * @return string
     */
    private function createRiffChunk($dataChunk)
    {
        return pack('A4Va4', 'RIFF', 4 + 24 + strlen($dataChunk), 'WAVE');
    }

    /**
     * @return string
     */
    private function createFmtChunk()
    {
        return pack('A4VvvVVvv', 'fmt', 16, 1, 1, 8000, 16000, 2, 16);
    }

    /**
     * @param string $data
     * @return string
     */
    private function createDataChunk($data)
    {
        return pack('A4V', 'data', strlen($data)) . $data;
    }

    /**
     * The raw audio files are supposed to contain mono *unsigned* 16-bit LPCM
     * samples with a sampling rate of 8000 Hz in little-endian byte order.
     *
     * @param string $code
     * @return ?int[]
     */
    private function concatenateRawAudio($code)
    {
        $data = '';
        for ($i = 0; $i < strlen($code); $i++) {
            $filename = $this->audioFolder . strtolower($code[$i]) . '.raw';
            if (is_readable($filename) && ($contents = file_get_contents($filename))) {
                $data .= $contents;
            } else {
                return null;
            }
        }
        $binary = unpack('v*', $data);
        assert($binary !== false);
        return $binary;
    }

    /**
     * @param int[] $samples
     * @return string
     */
    private function applyWhiteNoise($samples)
    {
        $gain = (65535 - self::NOISE_PEAK) / $this->getPeak($samples);
        ob_start();
        foreach ($samples as $sample) {
            echo pack('v', (int) ($gain * $sample) + mt_rand(0, self::NOISE_PEAK) - 32768);
        }
        $string = ob_get_clean();
        assert($string !== false);
        return $string;
    }

    /**
     * @param int[] $samples
     * @return int
     */
    private function getPeak($samples)
    {
        $peak = 0;
        foreach ($samples as $sample) {
            if ($sample > $peak) {
                $peak = $sample;
            }
        }
        return $peak;
    }
}
