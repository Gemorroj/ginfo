<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\Parser;
use Symfony\Component\Process\Process;

/**
 * Get nvidia card temps from nvidia-smmi.
 *
 * @author Joseph Gillotti
 */
class Nvidia implements Parser
{
    public static function work(): ?array
    {
        $process = new Process(['nvidia-smi', '-L'], null, ['LANG' => 'C']);
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

            $processCard = new Process(['nvidia-smi', 'dmon', '-s', 'p', '-c', '1', '-i', $id], null, ['LANG' => 'C']);
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
                    'name' => $name.' Power',
                    'value' => $match[2],
                    'unit' => 'W',
                ];
                $result[] = [
                    'path' => null,
                    'name' => $name.' Temperature',
                    'value' => $match[3],
                    'unit' => 'C',
                ];
            }
        }

        return $result;
    }
}
