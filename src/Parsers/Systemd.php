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


class Systemd implements Parser
{
    final private function __construct()
    {
    }

    final private function __clone()
    {
    }

    /**
     * @return array
     */
    public static function work()
    {
        $out = [];

        $list = (new Process('systemctl list-units --type service --all'))->mustRun()->getOutput();
        $lines = \explode("\n", \explode("\n\n", $list, 2)[0]);
        unset($lines[0]);

        foreach ($lines as $line) {
            $line = \trim($line);
            list($unit, $load, $active, $sub, $description) = \preg_split('/\s+/', $line, 5);

            $out[] = [
                'name' => $unit,
                'load' => $load,
                'active' => $active,
                'sub' => $sub,
                'description' => $description,
            ];
        }

        return $out;
    }
}
