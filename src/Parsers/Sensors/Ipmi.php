<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * IPMI extension for temps/voltages.
 *
 * @author Joseph Gillotti
 */
class Ipmi implements ParserInterface
{
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
                default => null,
            };

            $out[] = [
                'path' => null,
                'name' => \trim($m[1]),
                'value' => $vParts[0],
                'unit' => $unit,
            ];
        }

        return $out;
    }
}
