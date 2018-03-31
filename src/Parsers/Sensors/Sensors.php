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
 *
 */

namespace Linfo\Parsers\Sensors;

use Linfo\Parsers\Parser;
use Symfony\Component\Process\Process;


class Sensors implements Parser
{
    final private function __construct()
    {
    }

    final private function __clone()
    {
    }


    public static function work() : ?array
    {
        $process = new Process('sensors');
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }

        $list = \explode("\n", \trim($process->getOutput()));
        $return = [];
        foreach ($list as $line) {
            if (self::isSensorLine($line)) {
                $return[] = self::parseSensor($line);
            }
        }

        return $return;
    }

    private static function isSensorLine(string $line): bool
    {
        return false !== \strpos($line, ':') && 'Adapter:' !== \substr($line, 0, 8);
    }

    private static function parseSensor(string $sensor): array
    {
        list($name, $tmpStr) = \explode(':', $sensor, 2);
        $tmpStr = \ltrim($tmpStr);

        if (false !== \strpos($tmpStr, '°')) { // temperature
            list($value, $afterValue) = \explode('°', $tmpStr, 2);
            $unit = $afterValue[0]; // C
        } else {
            list($value, $unit, ) = \explode(' ', $tmpStr, 3); //V | RPM
        }

        return [
            'path' => null,
            'name' => $name,
            'value' => $value,
            'unit' => $unit,
        ];
    }
}
