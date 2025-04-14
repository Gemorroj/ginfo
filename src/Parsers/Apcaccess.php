<?php

namespace Ginfo\Parsers;

use Ginfo\Common;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Apcaccess implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{name: string, model: string, batteryVolts: float, batteryCharge: float, timeLeft: int, currentLoad: float, status: string}|null
     */
    public static function work(): ?array
    {
        $process = new Process(['apcaccess', 'status'], null, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $result = \trim($process->getOutput());
        if (\str_starts_with($result, 'Error')) {
            return null;
        }

        $block = Common::parseKeyValueBlock($result);

        return [
            'name' => $block['UPSNAME'],
            'model' => $block['MODEL'],
            'batteryVolts' => (float) \rtrim($block['BATTV'], ' Volts'),
            'batteryCharge' => (float) \rtrim($block['BCHARGE'], ' Percent'),
            'timeLeft' => (int) \rtrim($block['Minutes'], ' Minutes') * 60,
            'currentLoad' => (float) \rtrim($block['LOADPCT'], ' Percent'),
            'status' => $block['STATUS'],
        ];
    }
}
