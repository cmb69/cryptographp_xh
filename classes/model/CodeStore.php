<?php

/**
 * Copyright 2021 Christoph M. Becker
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

use Exception;

/**
 * A persistent hashtable with expiration
 *
 * Based on ideas an algorithms of
 * <https://en.wikipedia.org/wiki/Open_addressing>
 */
class CodeStore
{
    private const START_SIZE = 255;

    private const MAX_FILL = 0.5;

    private const GROTH_FACTOR = 4;

    private const HEADER_SIZE = 4 + 4 + 24;

    private const KEY_SIZE = 15;

    private const CODE_SIZE = 12;

    private const RECORD_SIZE = 1 + self::KEY_SIZE + 4 + self::CODE_SIZE;

    private const CHUNK_SIZE = 8192;

    /** @var string */
    private $filename;

    /** @var int */
    private $timestamp;

    /** @var int */
    private $retention;

    /** @var resource */
    private $stream;

    /** @var int */
    private $total;

    /** @var int */
    private $occupied;

    public function __construct(string $filename, int $timestamp, int $retention)
    {
        $this->filename = $filename;
        $this->timestamp = $timestamp;
        $this->retention = $retention;
    }

    public function find(string $key): ?string
    {
        assert(strlen($key) <= self::KEY_SIZE);

        try {
            $this->begin(false);
            $slot = $this->findSlot($key);
            $record = $this->readRecord($slot);
            if ($record["occupied"] && !$this->isExpired($record)) {
                $result = $record["code"];
            } else {
                $result = null;
            }
            $this->commit();
            return $result;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function put(string $key, string $code): bool
    {
        assert(strlen($key) <= self::KEY_SIZE);
        assert(strlen($code) <= self::CODE_SIZE);

        try {
            $this->begin(true);
            $slot = $this->findSlot($key);
            $record = $this->readRecord($slot);
            if ($record["occupied"]) {
                $record["timestamp"] = $this->timestamp;
                $record["code"] = $code;
                $this->writeRecord($slot, $record);
            } else {
                if ($this->occupied / $this->total >= self::MAX_FILL) {
                    $this->rebuild();
                }
                $record = ["occupied" => true, "key" => $key, "timestamp" => $this->timestamp, "code" => $code];
                $slot = $this->findSlot($key);
                $this->writeRecord($slot, $record);
                $this->occupied++;
                rewind($this->stream);
                fwrite($this->stream, pack("VV", $this->total, $this->occupied));
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function invalidate(string $key): bool
    {
        assert(strlen($key) <= self::KEY_SIZE);

        try {
            $this->begin(true);
            $slot = $this->findSlot($key);
            $record = $this->readRecord($slot);
            if ($record["occupied"] && !$this->isExpired($record)) {
                $record["timestamp"] = 0;
                $this->writeRecord($slot, $record);
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /** @return void */
    private function rebuild()
    {
        $all = $this->all();
        $nuls = str_repeat("\0", self::CHUNK_SIZE);
        rewind($this->stream);
        for ($i = 0; $i < ($this->total + 1) * self::RECORD_SIZE / self::CHUNK_SIZE; $i++) {
            fwrite($this->stream, $nuls);
        }
        $this->occupied = count($all);
        $total = self::GROTH_FACTOR * $this->occupied;
        $total = (int) pow(2, ceil(log($total) / log(2))) - 1;
        $this->total = max($total, self::START_SIZE);
        rewind($this->stream);
        $bytes = pack("VV", $this->total, $this->occupied);
        fwrite($this->stream, $bytes);
        ftruncate($this->stream, self::HEADER_SIZE + $this->total * self::RECORD_SIZE);
        foreach ($all as $record) {
            $slot = $this->findSlot($record["key"]);
            $this->writeRecord($slot, $record);
        }
    }

    /** @return array<array{occupied:bool,key:string,timestamp:int,code:string}> */
    private function all(): array
    {
        $results = [];
        for ($slot = 0; $slot < $this->total; $slot++) {
            $record = $this->readRecord($slot);
            if (!$record["occupied"] || $this->isExpired($record)) {
                continue;
            }
            $results[] = $record;
        }
        return $results;
    }

    private function findSlot(string $key): int
    {
        $slot = $this->hash($key) % $this->total;
        $record = $this->readRecord($slot);
        while ($record["occupied"] && $record["key"] !== $key) {
            $slot = ($slot + 1) % $this->total;
            $record = $this->readRecord($slot);
        }
        return $slot;
    }

    /** @param array{occupied:bool,key:string,timestamp:int,code:string} $record */
    private function isExpired(array $record): bool
    {
        return $record["timestamp"] + $this->retention < $this->timestamp;
    }

    /** @return void */
    private function begin(bool $exclusive)
    {
        $stream = fopen($this->filename, "c+");
        if ($stream === false) {
            throw new Exception("file could not be opened");
        }
        $this->stream = $stream;
        flock($this->stream, $exclusive ? LOCK_EX : LOCK_SH);
        $bytes = fread($this->stream, self::HEADER_SIZE);
        if ($bytes === false) {
            throw new Exception("could not read from stream");
        }
        if (strlen($bytes) < self::HEADER_SIZE) {
            $bytes = pack("VV", self::START_SIZE, 0);
            fwrite($this->stream, $bytes);
            ftruncate($this->stream, self::HEADER_SIZE + self::START_SIZE * self::RECORD_SIZE);
        }
        $header = unpack("Vtotal/Voccupied", $bytes);
        if ($header === false) {
            throw new Exception("corrupt stream");
        }
        $this->total = $header["total"];
        $this->occupied = $header["occupied"];
    }

    /** @return void */
    private function commit()
    {
        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

    /** @return array{occupied:bool,key:string,timestamp:int,code:string} */
    private function readRecord(int $slot): array
    {
        fseek($this->stream, self::HEADER_SIZE + $slot * self::RECORD_SIZE);
        $data = fread($this->stream, self::RECORD_SIZE);
        if ($data === false) {
            throw new Exception("could not read from stream");
        }
        $result = unpack("coccupied/a15key/Vtimestamp/a12code", $data);
        if (!is_array($result) || !isset($result["occupied"], $result["key"], $result["timestamp"])) {
            throw new Exception("corrupt stream");
        }
        $result["occupied"] = (bool) $result["occupied"];
        $result["code"] = rtrim($result["code"], "\0");
        return $result;
    }

    /**
     * @param array{occupied:bool,key:string,timestamp:int,code:string} $record
     * @return void
     */
    private function writeRecord(int $slot, array $record)
    {
        fseek($this->stream, self::HEADER_SIZE + $slot * self::RECORD_SIZE);
        fwrite(
            $this->stream,
            pack("ca15Va12", $record["occupied"], $record["key"], $record["timestamp"], $record["code"])
        );
    }

    private function hash(string $key): int
    {
        $hash = 0;
        for ($i = PHP_INT_SIZE - 1; $i >= 0; $i--) {
            $hash += (256 ** $i) * ($i === PHP_INT_SIZE - 1 ? ord($key[$i]) & 0x7f : ord($key[$i]));
        }
        return $hash;
    }
}
