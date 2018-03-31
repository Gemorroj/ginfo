<?php

/**
 * This file is part of Linfo (c) 2011 Joseph Gillotti.
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

/**
 * IPMI extension for temps/voltages.
 *
 * @author Joseph Gillotti
 */
class Ipmi implements Parser
{
    public static function work() : ?array
    {
        $process = new Process('ipmitool sdr', null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = $process->getOutput();

        if (!\preg_match_all('/^([^|]+)\| ([\d\.]+ (?:Volts|degrees [CF]))\s+\| ok$/m', $result, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $out = [];
        foreach ($matches as $m) {
            $vParts = \explode(' ', \trim($m[2]));

            switch ($vParts[1]) {
                case 'Volts':
                    $unit = 'V';
                    break;
                case 'degrees':
                    $unit = $vParts[2];
                    break;
                default:
                    $unit = null;
                    break;
            }

            $out[] = [
                'path' => null,
                'name' => \trim($m[1]),
                'value' => $vParts[0],
                'unit' => $unit,
            ];
        }

        return $out;
    }
}
