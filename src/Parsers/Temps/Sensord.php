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

namespace Linfo\Parsers\Temps;

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

    public static function work()
    {
        $obj = new self();
        return $obj->parseSysLog();
    }

    /**
     * For parsing the syslog looking for sensord entries
     * POTENTIALLY BUGGY -- only tested on debian/ubuntu flavored syslogs
     * Also slow as balls as it parses the entire syslog instead of
     * using something like tail
     * @return array
     */
    private function parseSysLog()
    {
        $lines = Common::getLines('/var/log/syslog');
        if (!$lines) {
            return null;
        }

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
                'temp' => $stat,
                'unit' => null, // Usually included in above
            ];
        }

        return $return;
    }
}
