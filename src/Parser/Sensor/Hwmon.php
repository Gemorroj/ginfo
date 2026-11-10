<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\CommonTrait;
use Ginfo\Parser\ParserInterface;

final readonly class Hwmon implements ParserInterface
{
    use CommonTrait;

    /**
     * @return array{path: string|null, name: string, value: float, unit: string}[]|null
     */
    public function run(): ?array
    {
        $paths = \glob('/sys/class/hwmon/hwmon*/{,device/}*_input', \GLOB_NOSORT | \GLOB_BRACE);
        if (!$paths) {
            return null;
        }

        $hwmonVals = [];
        foreach ($paths as $path) {
            $value = self::getContents($path);
            if (null === $value) {
                continue;
            }

            $sensor = self::parseSensor(
                \basename($path),
                $value,
                self::getContents(\substr($path, 0, -\strlen('input')).'label'),
                self::getContents(\dirname($path).'/device/model'),
                self::getContents(\dirname($path).'/name'),
            );
            if (null === $sensor) {
                continue;
            }

            $hwmonVals[] = $sensor;
        }

        return $hwmonVals;
    }

    /**
     * @return array{path: string|null, name: string, value: float, unit: string}|null
     */
    private static function parseSensor(string $base, string $value, ?string $label, ?string $model, ?string $driverName): ?array
    {
        $value = (float) $value;

        // Temperatures
        if (\str_starts_with($base, 'temp') && null !== $label) {
            $name = $label;
            $value /= $value > 10000 ? 1000 : 1;
            $unit = 'C'; // I don't think this is ever going to be in F
        }
        // Devices (such as hard drives)
        elseif (\str_starts_with($base, 'temp') && null !== $model) {
            $name = $model;
            $value /= $value > 10000 ? 1000 : 1;
            $unit = 'C'; // I don't think this is ever going to be in F
        }
        // Fan RPMs
        elseif (\preg_match('/^fan(\d+)_/', $base, $m)) {
            $name = 'fan'.$m[1];
            $unit = 'RPM';
        }
        // Volts
        elseif (\preg_match('/^in(\d+)_/', $base, $m)) {
            $unit = 'V';
            $value /= 1000;
            $name = $label ?: 'in'.$m[1];
        } else {
            return null;
        }

        return [
            'path' => null,
            'name' => $name.(null !== $driverName && '' !== $driverName ? ' ('.$driverName.')' : ''),
            'value' => $value,
            'unit' => $unit,
        ];
    }
}
