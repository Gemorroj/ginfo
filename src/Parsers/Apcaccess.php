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

namespace Linfo\Parsers;

use Linfo\Common;
use Symfony\Component\Process\Process;


class Apcaccess implements Parser
{
    public static function work() : ?array
    {
        $process = new Process('apcaccess status', null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = \trim($process->getOutput());
        if ('Error' === \substr($result, 0, 5)) {
            return null;
        }

        $block = Common::parseKeyValueBlock($result);

        return [
            'name' => $block['UPSNAME'],
            'model' => $block['MODEL'],
            'batteryVolts' => \rtrim($block['BATTV'], ' Volts'),
            'batteryCharge' => \rtrim($block['BCHARGE'], ' Percent'),
            'timeLeft' => \rtrim($block['Minutes'], ' Minutes') * 60,
            'currentLoad' => \rtrim($block['LOADPCT'], ' Percent'),
            'status' => $block['STATUS'],
        ];
    }
}
