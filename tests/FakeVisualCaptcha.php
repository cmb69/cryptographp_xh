<?php

/**
 * Copyright 2023 Christoph M. Becker
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

class FakeVisualCaptcha extends VisualCaptcha
{
    protected function randomFont(int $count): int
    {
        return 0;
    }

    protected function randomAngle(int $max): int
    {
        return 0;
    }

    protected function randomCharSize(int $min, int $max): int
    {
        return 15;
    }

    protected function randomDisplacement(int $min, int $max): int
    {
        return 0;
    }

    protected function randomBackgroundImage(int $count): int
    {
        return 0;
    }

    /** @return array{int,int,int} */
    protected function randomNoiseColor(): array
    {
        return [0, 0, 255];
    }

    /** @return array{int,int,int} */
    protected function randomCharColor(): array
    {
        return [0, 255, 0];
    }

    protected function randomPointCount(int $min, int $max): int
    {
        return 1;
    }

    protected function randomLineCount(int $min, int $max): int
    {
        return 1;
    }

    protected function randomCircleCount(int $min, int $max): int
    {
        return 1;
    }

    /** @return array{int,int} */
    protected function randomPoint(int $width, int $height): array
    {
        return [2, 2];
    }

    /** @return array{int,int,int,int} */
    protected function randomLine(int $width, int $height): array
    {
        return [10, 10, 120, 30];
    }

    /** @return array{int,int,int} */
    protected function randomCircle(int $width, int $height): array
    {
        return [65, 20, 20];
    }
}
