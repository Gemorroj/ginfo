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
 * Get info on a samba install by running smbstatus
 */
class Smbstatus implements Parser
{
    public static function work() : ?array
    {
        $process = new Process('smbstatus');
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = $process->getOutput();
        $lines = \explode("\n", $result);

        $res = [
            'connections' => [],
            'services' => [],
            'files' => [],
        ];
        $currentLocation = null;

        foreach ($lines as $line) {
            $line = \trim($line);

            if ($line === '' || \preg_match('/^\-+$/', $line)) {
                continue;
            } // Beginning connections list?
            elseif (\preg_match('/^PID\s+Username\s+Group\s+Machine$/', $line)) {
                $currentLocation = 'c';
            } // A connection?
            elseif ($currentLocation === 'c' && \preg_match('/^(\d+)\s+(\w+)\s+(\w+)\s+(\S+)\s+\(([^)]+)\)$/', $line, $connectionMatch)) {
                $res['connections'][] = [
                    'pid' => $connectionMatch[1],
                    'username' => $connectionMatch[2],
                    'group' => $connectionMatch[3],
                    'machine' => $connectionMatch[4],
                    'protocolVersion' => $connectionMatch[5],
                ];
            } // Beginning services list?
            elseif (\preg_match('/^Service\s+pid\s+machine\s+Connected at$/', $line)) {
                $currentLocation = 's';
            } // A service?
            elseif ($currentLocation === 's' && \preg_match('/^(\w+)\s+(\d+)\s+(\S+)\s+([a-zA-z]+ [a-zA-Z]+ \d+ \d+:\d+:\d+ \d+)$/', $line, $serviceMatch)) {
                $res['services'][] = [
                    'service' => $serviceMatch[1],
                    'pid' => $serviceMatch[2],
                    'machine' => $serviceMatch[3],
                    'date' => new \DateTime($serviceMatch[4]),
                ];
            } // Beginning locked files list?
            elseif (\preg_match('/^Pid\s+Uid\s+DenyMode\s+Access\s+R\/W\s+Oplock\s+SharePath\s+Name\s+Time$/', $line)) {
                $currentLocation = 'f';
            } // A locked file?
            elseif ($currentLocation === 'f' && \preg_match('/^(\d+)\s+(\d+)\s+(\S+)\s+(\S+)\s+([A-Z]+)\s+([A-Z+]+)\s+(\S+)\s+(.+)\s+([a-zA-Z]+ [a-zA-Z]+ \d+ \d+:\d+:\d+ \d+)$/', $line, $fileMatch)) {
                $res['files'][] = [
                    'pid' => $fileMatch[1],
                    'user' => \posix_getpwuid($fileMatch[2])['name'],
                    'denyMode' => $fileMatch[3],
                    'access' => $fileMatch[4],
                    'rw' => $fileMatch[5],
                    'oplock' => $fileMatch[6],
                    'sharePath' => $fileMatch[7],
                    'name' => $fileMatch[8],
                    'date' => new \DateTime($fileMatch[9]),
                ];
            }
        }

        return $res;
    }
}
