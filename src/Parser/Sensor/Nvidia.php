<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * Get nvidia card temps from nvidia-smmi.
 */
final readonly class Nvidia implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{path: string|null, name: string, value: float, unit: string}[]|null
     */
    public function run(?string $cwd = null): ?array
    {
        $process = new Process(['nvidia-smi', '-L'], $cwd, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $cardsList = $process->getOutput();

        $cards = self::parseCards($cardsList);
        if (null === $cards) {
            return null;
        }

        $result = [];
        foreach ($cards as $card) {
            $processCard = new Process(['nvidia-smi', 'dmon', '-s', 'p', '-c', '1', '-i', $card['id']], $cwd, ['LANG' => 'C']);
            $processCard->run();
            if (!$processCard->isSuccessful()) {
                continue;
            }

            foreach (self::parseCard($card['id'], $card['name'], $processCard->getOutput()) as $sensor) {
                $result[] = $sensor;
            }
        }

        return $result;
    }

    /**
     * @return array<int, array{id: string, name: string}>|null
     */
    private static function parseCards(string $output): ?array
    {
        if (!\preg_match_all('/GPU (\d+): (.+) \(UUID:.+\)/', $output, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $cards = [];
        foreach ($matches as $card) {
            $cards[] = [
                'id' => $card[1],
                'name' => \trim($card[2]),
            ];
        }

        return $cards;
    }

    /**
     * @return array<int, array{path: string|null, name: string, value: float, unit: string}>
     */
    private static function parseCard(string $id, string $name, string $output): array
    {
        if (!\preg_match('/(\d+)\s+(\d+)\s+(\d+)/', $output, $match) || $match[1] !== $id) {
            return [];
        }

        return [
            [
                'path' => null,
                'name' => $name.' Power',
                'value' => (float) $match[2],
                'unit' => 'W',
            ],
            [
                'path' => null,
                'name' => $name.' Temperature',
                'value' => (float) $match[3],
                'unit' => 'C',
            ],
        ];
    }
}
