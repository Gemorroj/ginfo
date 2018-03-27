<?php

/**
 * This file is part of Linfo (c) 2010 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Linfo\Parsers;


use Symfony\Component\Process\Process;

class Free
{
    public static function work()
    {
        $free = (new Process('free -bw'))->mustRun()->getOutput();

        $arr = \explode("\n", $free);
        unset($arr[0]); // remove header

        $memStr = \trim(\ltrim($arr[1], 'Mem:'));
        $swapStr = \trim(\ltrim($arr[2], 'Swap:'));

        list($memTotal, $memUsed, $memFree, $memShared, $memBuffers, $memCached, $memAvailable) = \preg_split('/\s+/', $memStr);
        list($swapTotal, $swapUsed, $swapFree) = \preg_split('/\s+/', $swapStr);

        return [
            'memoryTotal' => $memTotal,
            'memoryUsed' => $memUsed,
            'memoryFree' => $memFree,
            'memoryShared' => $memShared,
            'memoryBuffers' => $memBuffers,
            'memoryCached' => $memCached,

            'swapTotal' => $swapTotal,
            'swapUsed' => $swapUsed,
            'swapFree' => $swapFree,
        ];
    }
}
