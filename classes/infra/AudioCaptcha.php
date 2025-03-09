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

namespace Cryptographp\Infra;

class AudioCaptcha
{
    private const NOISE_PEAK = 4000;
    private const RIFF_TAG = "RIFF";
    private const RIFF_TYPE = "WAVE";
    private const FMT_TAG = "fmt ";
    private const FMT_CHUNK_SIZE = 16;
    private const PCM_FORMAT = 1;
    private const CHANNELS = 1;
    private const SAMPLES_PER_SEC = 8000;
    private const BYTES_PER_SAMPLE = 2;
    private const BITS_PER_SAMPLE = self::BYTES_PER_SAMPLE * 8;
    private const BYTES_PER_SEC = self::SAMPLES_PER_SEC * self::BYTES_PER_SAMPLE;
    private const DATA_TAG = "data";

    /** @var string */
    private $audioFolder;

    public function __construct(string $audioFolder)
    {
        $this->audioFolder = $audioFolder;
    }

    public function createWav(string $lang, string $code): ?string
    {
        $samples = $this->concatenateRawAudio($lang, $code);
        if ($samples === null) {
            return null;
        }
        $dataChunk = $this->createDataChunk($this->applyWhiteNoise($samples));
        return $this->createRiffChunk($dataChunk) . $this->createFmtChunk() . $dataChunk;
    }

    private function createRiffChunk(string $dataChunk): string
    {
        return pack("A4Va4", self::RIFF_TAG, 4 + 24 + strlen($dataChunk), self::RIFF_TYPE);
    }

    private function createFmtChunk(): string
    {
        return pack(
            "A4VvvVVvv",
            self::FMT_TAG,
            self::FMT_CHUNK_SIZE,
            self::PCM_FORMAT,
            self::CHANNELS,
            self::SAMPLES_PER_SEC,
            self::BYTES_PER_SEC,
            self::BYTES_PER_SAMPLE,
            self::BITS_PER_SAMPLE
        );
    }

    private function createDataChunk(string $data): string
    {
        return pack("A4V", self::DATA_TAG, strlen($data)) . $data;
    }

    /**
     * The raw audio files are supposed to contain mono *unsigned* 16-bit LPCM
     * samples with a sampling rate of 8000 Hz in little-endian byte order.
     *
     * @return ?array<int>
     */
    private function concatenateRawAudio(string $lang, string $code): ?array
    {
        if (!is_dir($this->audioFolder . $lang)) {
            $lang = "en";
        }
        $data = "";
        for ($i = 0; $i < strlen($code); $i++) {
            $filename = $this->audioFolder . $lang . "/" . strtolower($code[$i]) . ".raw";
            if (!is_readable($filename)) {
                return null;
            }
            $contents = file_get_contents($filename);
            if ($contents === false) {
                return null;
            }
            $data .= $contents;
        }
        $binary = unpack("v*", $data);
        if ($binary === false) {
            return null;
        }
        return $binary;
    }

    /** @param array<int> $samples */
    private function applyWhiteNoise(array $samples): string
    {
        $gain = ((1 << self::BITS_PER_SAMPLE) - 1 - self::NOISE_PEAK) / $this->peak($samples);
        // loop instead of array_map() for performance reasons
        $new = [];
        foreach ($samples as $sample) {
            $new[] = pack("v", (int) ($gain * $sample) + $this->randomGain() - (1 << self::BITS_PER_SAMPLE) / 2);
        }
        return implode("", $new);
    }

    /** @param array<int> $samples */
    private function peak(array $samples): int
    {
        // loop instead of array_reduce() for performance reasons
        $peak = 0;
        foreach ($samples as $sample) {
            if ($sample > $peak) {
                $peak = $sample;
            }
        }
        return $peak;
    }

    /** @codeCoverageIgnore */
    protected function randomGain(): int
    {
        return mt_rand(0, self::NOISE_PEAK);
    }
}
