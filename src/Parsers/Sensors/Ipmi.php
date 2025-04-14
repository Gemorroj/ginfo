<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * IPMI extension for temps/voltages.
 */
final readonly class Ipmi implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{path: string|null, name:  string, value: float, unit: string}|null
     */
    public static function work(): ?array
    {
        $process = new Process(['ipmitool', 'sdr'], null, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $result = $process->getOutput();

        if (!\preg_match_all('/^([^|]+)\| ([\d\.]+ (?:Volts|degrees [CF]))\s+\| ok$/m', $result, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $out = [];
        foreach ($matches as $m) {
            $vParts = \explode(' ', \trim($m[2]));

            $unit = match ($vParts[1]) {
                'Volts' => 'V',
                'degrees' => $vParts[2],
                default => '',
            };

            $out[] = [
                'path' => null,
                'name' => \trim($m[1]),
                'value' => (float) $vParts[0],
                'unit' => $unit,
            ];
        }

        return $out;
    }
}
