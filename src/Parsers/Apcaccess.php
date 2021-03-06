<?php

namespace Ginfo\Parsers;

use Ginfo\Common;
use Symfony\Component\Process\Process;

class Apcaccess implements ParserInterface
{
    public static function work(): ?array
    {
        $process = new Process(['apcaccess', 'status'], null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = \trim($process->getOutput());
        if (0 === \strpos($result, 'Error')) {
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
