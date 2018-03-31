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
 * Get info on a cups install by running lpstat
 */
class Lpstat implements Parser
{
    public static function work() : ?array
    {
        $process = new Process('lpstat -p', null, ['LANG' => 'C']);
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }

        $lines = \explode("\n", \trim($process->getOutput()));

        $res = [];
        foreach ($lines as $line) {
            $line = \trim($line);

            if (\preg_match('/^printer (\w+) .*([enabled|disabled]+) since .+?/Uu', $line, $printersMatch)) {
                $res[] = [
                    'name' => \str_replace('_', ' ', $printersMatch[1]),
                    'enabled' => 'enabled' === $printersMatch[2],
                ];
            }
        }

        return $res;
    }
}
