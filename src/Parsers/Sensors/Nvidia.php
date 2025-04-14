<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * Get nvidia card temps from nvidia-smmi.
 */
final readonly class Nvidia implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{path: string|null, name: string, value: float, unit: string}[]|null
     */
    public static function work(): ?array
    {
        $process = new Process(['nvidia-smi', '-L'], null, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
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
                if ($match[1] !== $id) {
                    continue;
                }

                $result[] = [
                    'path' => null,
                    'name' => $name.' Power',
                    'value' => (float) $match[2],
                    'unit' => 'W',
                ];
                $result[] = [
                    'path' => null,
                    'name' => $name.' Temperature',
                    'value' => (float) $match[3],
                    'unit' => 'C',
                ];
            }
        }

        return $result;
    }
}
