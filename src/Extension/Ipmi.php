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

namespace Linfo\Extension;

use Linfo\Linfo;
use Linfo\Meta\Errors;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * IPMI extension for temps/voltages.
 *
 * @author Joseph Gillotti
 */
class Ipmi implements Extension
{
    // Minimum version of Linfo required
    const LINFO_INTEGRATE = true;
    const EXTENSION_NAME = 'ipmi';

    private $linfo;

    // Start us off
    public function __construct(Linfo $linfo)
    {
        $this->linfo = $linfo;
    }

    // Work it, baby
    public function work()
    {
        $info = &$this->linfo->getInfo();

        // Make sure this is an array
        $info['Temps'] = (array)$info['Temps'];

        // Deal with calling it
        try {
            $process = new Process('ipmitool sdr');
            $process->mustRun();
            $result = $process->getOutput();
        } catch (ProcessFailedException $e) {
            // messed up somehow
            Errors::add(self::EXTENSION_NAME . ' Extension', $e->getMessage());
            return;
        }

        // Match it up
        if (!preg_match_all('/^([^|]+)\| ([\d\.]+ (?:Volts|degrees [CF]))\s+\| ok$/m', $result, $matches, PREG_SET_ORDER)) {
            return;
        }

        // Go through with it
        foreach ($matches as $m) {

            // Separate them by normal spaces
            $v_parts = explode(' ', trim($m[2]));

            // Deal with the type of it
            switch ($v_parts[1]) {
                case 'Volts':
                    $unit = 'v';
                    break;
                case 'degrees':
                    $unit = $v_parts[2];
                    break;
                default:
                    $unit = '';
                    break;
            }

            // Save this one
            $info['Temps'][] = array(
                'path' => 'N/A',
                'name' => trim($m[1]),
                'temp' => $v_parts[0],
                'unit' => $unit,
            );
        }
    }

    // Not needed
    public function result()
    {
        return false;
    }
}
