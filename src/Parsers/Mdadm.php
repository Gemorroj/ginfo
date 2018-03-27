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


use Linfo\Common;

class Mdadm
{
    public static function work()
    {
        $mdadmContents = Common::getContents('/proc/mdstat');
        if (null === $mdadmContents) {
            return null;
        }

        if (false === @\preg_match_all('/(\S+)\s*:\s*(\w+)\s*raid(\d+)\s*([\w+\[\d+\] (\(\w\))?]+)\n\s+(\d+) blocks[^[]+\[(\d\/\d)\] \[([U\_]+)\]/mi', (string)$mdadmContents, $match, \PREG_SET_ORDER)) {
            return null;
        }

        $mdadmArrays = [];
        foreach ((array)$match as $array) {

            $drives = [];
            foreach (\explode(' ', $array[4]) as $drive) {
                if (\preg_match('/([\w\d]+)\[\d+\](\(\w\))?/', $drive, $matchDrive) === 1) {
                    // Determine a status other than normal, like if it failed or is a spare
                    if (\array_key_exists(2, $matchDrive)) {
                        switch ($matchDrive[2]) {
                            case '(S)':
                                $driveState = 'spare';
                                break;
                            case '(F)':
                                $driveState = 'failed';
                                break;
                            case null:
                                $driveState = 'normal';
                                break;

                            // I'm not sure if there are status codes other than the above
                            default:
                                $driveState = 'unknown';
                                break;
                        }
                    } else {
                        $driveState = 'normal';
                    }

                    $drives[] = [
                        'drive' => '/dev/' . $matchDrive[1],
                        'state' => $driveState,
                    ];
                }
            }

            $mdadmArrays[] = [
                'device' => '/dev/' . $array[1],
                'status' => $array[2],
                'level' => $array[3],
                'drives' => $drives,
                'size' => $array[5] * 1024,
                'count' => $array[6],
                'chart' => $array[7],
            ];
        }

        return $mdadmArrays;
    }
}
