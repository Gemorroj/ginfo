<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Sensors implements ParserInterface
{
    /**
     * @return array{path: string|null, name: string, value: float, unit: string}[]|null
     */
    public function run(?string $cwd = null): ?array
    {
        $process = new Process(['sensors'], $cwd, ['LANG=C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
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
        return \str_contains($line, ':') && !\str_starts_with($line, 'Adapter:');
    }

    private static function parseSensor(string $sensor): array
    {
        [$name, $tmpStr] = \explode(':', $sensor, 2);
        $tmpStr = \ltrim($tmpStr);

        if (\str_contains($tmpStr, '°')) { // temperature
            [$value, $afterValue] = \explode('°', $tmpStr, 2);
            $unit = $afterValue[0]; // C
        } else {
            [$value, $unit] = \explode(' ', $tmpStr, 3); // V | RPM
        }

        return [
            'path' => null,
            'name' => $name,
            'value' => (float) $value,
            'unit' => $unit,
        ];
    }
}
