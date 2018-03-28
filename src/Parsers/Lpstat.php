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

namespace Linfo\Parsers;

use Symfony\Component\Process\Process;

/**
 * Get info on a cups install by running lpq
 */
class Lpstat implements Parser
{
    public static function work()
    {
        $process = new Process('lpstat -p -o -l');
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }

        $result = $process->getOutput();

        $lines = \explode("\n", $result);


        $res = [
            'printers' => [],
            'queue' => [],
        ];
        $beginQueueList = false;

        foreach ($lines as $line) {
            $line = \trim($line);

            // If there are no entries, don't waste time and end here
            if ($line === 'no entries') {
                break;
            } elseif (\preg_match('/^printer (.+) is idle\. (.+)$/', $line, $printersMatch) === 1) {
                $res['printers'][] = [
                    'name' => \str_replace('_', ' ', $printersMatch[1]),
                    'status' => $printersMatch[2],
                ];
            } // A printer entry
            elseif (\preg_match('/^(.+)+ is (ready|ready and printing|not ready)$/', $line, $printersMatch) === 1) {
                $res['printers'][] = [
                    'name' => \str_replace('_', ' ', $printersMatch[1]),
                    'status' => $printersMatch[2],
                ];
            } // The beginning of the queue list
            elseif (\preg_match('/^Rank\s+Owner\s+Job\s+File\(s\)\s+Total Size$/', $line)) {
                $beginQueueList = true;
            } // A job in the queue
            elseif ($beginQueueList && \preg_match('/^([a-z0-9]+)\s+(\S+)\s+(\d+)\s+(.+)\s+(\d+) bytes$/', $line, $queueMatch)) {
                $res['queue'][] = [
                    'rank' => $queueMatch[1],
                    'owner' => $queueMatch[2],
                    'job' => $queueMatch[3],
                    'files' => $queueMatch[4],
                    'size' => $queueMatch[5],
                ];
            }
        }

        return $res;
    }
}
