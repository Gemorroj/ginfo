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

use Linfo\Common;
use Linfo\Parsers\Parser;


class Sensord implements Parser
{
    final private function __construct()
    {
    }

    final private function __clone()
    {
    }

    /**
     * For parsing the syslog looking for sensord entries
     * POTENTIALLY BUGGY -- only tested on debian/ubuntu flavored syslogs
     * Also slow as balls as it parses the entire syslog instead of
     * using something like tail
     */
    public static function work() : ?array
    {
        $lines = Common::getLines('/var/log/syslog');
        if (!$lines) {
            return null;
        }

        /*
         * todo: fix parser & add tests
         * example outputs:
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: VCore: +1.63 V (min = +1.71 V, max = +1.89 V) [ALARM]
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: +5V: +4.68 V (min = +4.76 V, max = +5.24 V) [ALARM]
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: -12V: +1.70 V (min = -13.18 V, max = -10.80 V) [ALARM]
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: -5V: +2.44 V (min = -5.25 V, max = -4.75 V) [ALARM]
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: fan1: 0 RPM (min = -1 RPM, div = 8) [ALARM]
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: fan2: 3245 RPM (min = -1 RPM, div = 8) [ALARM]
Jun  1 01:50:37 localhost sensord: Sensor alarm: Chip w83697hf-isa-0290: temp1: 37 C (limit = 32 C, hysteresis = 32 C, sensors = PII/Celeron diode) [ALARM]

2015-10-20T17:27:20Z sensord: Warning: Unexpected error Failure
2015-10-20T17:27:20Z watchdog-sensord: '/usr/lib/vmware/bin/sensord ++min=0,max=10 -l' exited after 6627 seconds 1
2015-10-20T17:27:20Z watchdog-sensord: Executing '/usr/lib/vmware/bin/sensord ++min=0,max=10 -l'
2015-10-20T17:27:22Z sensord: Warning: Unsupported hardware

Nov 29 07:26:07 archie sensord: Chip: as99127f-i2c-0-2d
Nov 29 07:26:07 archie sensord: Adapter: SMBus I801 adapter at e800
Nov 29 07:26:07 archie sensord: Algorithm: Unavailable from sysfs
Nov 29 07:26:07 archie sensord: VCore 1: +1.73 V (min = +1.74 V, max = +1.94 V) [ALARM]
         */

        $devices = [];
        foreach ($lines as $line) {
            if (\preg_match('/\w+\s*\d+ \d{2}:\d{2}:\d{2} \w+ sensord:\s*(.+):\s*(.+)/i', \trim($line), $match) === 1) {
                // Replace current record of dev with updated temp
                $devices[$match[1]] = $match[2];
            }
        }
        $return = [];
        foreach ($devices as $dev => $stat) {
            $return[] = [
                'path' => null, // These likely won't have paths
                'name' => $dev,
                'value' => $stat,
                'unit' => null, // Usually included in above
            ];
        }

        return $return;
    }
}
