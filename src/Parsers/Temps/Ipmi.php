<?php

/*
This implements a ipmi status checker for temps/voltages

Installation: 
 - The following lines must be added to your settings:
   $settings['extensions']['ipmi'] = true; 

 - The ipmitool command most likely needs to be run as root, so, 
   if you don't have php running as root, configure sudo appropriately
   for the user the php scripts are running as, comment out 'Defaults    requiretty' in your sudoers
   file, and add 'ipmitool' to the $settings['sudo_apps'] array in settings
*/

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

namespace Linfo\Parsers\Temps;

use Linfo\Linfo;
use Linfo\Meta\Errors;
use Linfo\Parsers\Parser;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * IPMI extension for temps/voltages.
 *
 * @author Joseph Gillotti
 */
class Ipmi implements Parser
{
    public static function work()
    {
        $process = new Process('ipmitool sdr');
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
                    $unit = 'v';
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
                'temp' => $vParts[0],
                'unit' => $unit,
            ];
        }

        return $out;
    }
}
