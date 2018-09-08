<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\Parser;
use Symfony\Component\Process\Process;

class Sensors implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function work(): ?array
    {
        $process = new Process(['sensors'], null, ['LANG=C']);
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }

        $list = \explode("\n", \trim($process->getOutput()));
        $return = [];
        foreach ($list as $line) {
            if (self::isSensorLine($line)) {
                $return[] = self::parseSensor($line);
            }
        }

        return $return;
    }

    private static function isSensorLine(string $line): bool
    {
        return false !== \mb_strpos($line, ':') && 'Adapter:' !== \mb_substr($line, 0, 8);
    }

    private static function parseSensor(string $sensor): array
    {
        [$name, $tmpStr] = \explode(':', $sensor, 2);
        $tmpStr = \ltrim($tmpStr);

        if (false !== \mb_strpos($tmpStr, '°')) { // temperature
            [$value, $afterValue] = \explode('°', $tmpStr, 2);
            $unit = $afterValue[0]; // C
        } else {
            [$value, $unit, ] = \explode(' ', $tmpStr, 3); //V | RPM
        }

        return [
            'path' => null,
            'name' => $name,
            'value' => $value,
            'unit' => $unit,
        ];
    }
}
