<?php

/**
 * This file is part of Linfo (c) 2017 Joseph Gillotti.
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

use Linfo\Parsers\Parser;
use Symfony\Component\Process\Process;

/**
 * Get nvidia card temps from nvidia-smmi
 *
 * @author Joseph Gillotti
 */
class Nvidia implements Parser
{
    public static function work() : ?array
    {
        $process = new Process('nvidia-smi -L');
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }

        $cardsList = $process->getOutput();


        if (!\preg_match_all('/GPU (\d+): (.+) \(UUID:.+\)/', $cardsList, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $result = [];
        foreach ($matches as $card) {
            $id = $card[1];
            $name = \trim($card[2]);

            $processCard = new Process('nvidia-smi dmon -s p -c 1 -i ' . $id);
            $processCard->run();
            if (!$processCard->isSuccessful()) {
                continue;
            }

            $cardStat = $process->getOutput();

            if (\preg_match('/(\d+)\s+(\d+)\s+(\d+)/', $cardStat, $match)) {
                if ($match[1] != $id) {
                    continue;
                }

                $result[] = [
                    'path' => null,
                    'name' => $name . ' Power',
                    'value' => $match[2],
                    'unit' => 'W',
                ];
                $result[] = [
                    'path' => null,
                    'name' => $name . ' Temperature',
                    'value' => $match[3],
                    'unit' => 'C',
                ];
            }
        }

        return $result;
    }
}