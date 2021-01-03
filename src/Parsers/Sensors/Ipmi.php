<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\Parser;
use Symfony\Component\Process\Process;

/**
 * IPMI extension for temps/voltages.
 *
 * @author Joseph Gillotti
 */
class Ipmi implements Parser
{
    public static function work(): ?array
    {
        $process = new Process(['ipmitool', 'sdr'], null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = $process->getOutput();

        if (!\preg_match_all('/^([^|]+)\| ([\d\.]+ (?:Volts|degrees [CF]))\s+\| ok$/m', $result, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $out = [];
        foreach ($matches as $m) {
            $vParts = \explode(' ', \trim($m[2]));

            switch ($vParts[1]) {
                case 'Volts':
                    $unit = 'V';
                    break;
                case 'degrees':
                    $unit = $vParts[2];
                    break;
                default:
                    $unit = null;
                    break;
            }

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
