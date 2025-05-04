<?php

namespace Ginfo\Parser;

use Ginfo\Common;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Apcaccess implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{name: string, model: string, batteryVolts: float, batteryCharge: float, timeLeft: int, currentLoad: float, status: string}|null
     */
    public function run(?string $cwd = null): ?array
    {
        $process = new Process(['apcaccess', 'status'], $cwd, ['LANG' => 'C']);
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
        if (!isset($block['UPSNAME'])) {
            return null;
        }

        $batteryVolts = \trim(\str_ends_with($block['BATTV'], 'Volts') ? \substr($block['BATTV'], 0, \strlen('Volts')) : $block['BATTV']);
        $batteryCharge = \trim(\str_ends_with($block['BCHARGE'], 'Percent') ? \substr($block['BCHARGE'], 0, \strlen('Percent')) : $block['BCHARGE']);
        $timeLeft = \trim(\str_ends_with($block['Minutes'], 'Minutes') ? \substr($block['Minutes'], 0, \strlen('Minutes')) : $block['Minutes']);
        $currentLoad = \trim(\str_ends_with($block['LOADPCT'], 'Percent') ? \substr($block['LOADPCT'], 0, \strlen('Percent')) : $block['LOADPCT']);

        return [
            'name' => $block['UPSNAME'],
            'model' => $block['MODEL'],
            'batteryVolts' => (float) $batteryVolts,
            'batteryCharge' => (float) $batteryCharge,
            'timeLeft' => (int) $timeLeft * 60,
            'currentLoad' => (float) $currentLoad,
            'status' => $block['STATUS'],
        ];
    }
}
