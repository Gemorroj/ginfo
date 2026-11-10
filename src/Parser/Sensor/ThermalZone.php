<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\CommonTrait;
use Ginfo\Parser\ParserInterface;

final readonly class ThermalZone implements ParserInterface
{
    use CommonTrait;

    /**
     * @return array{path: string, name: string, value: float, unit: string}[]|null
     */
    public function run(): ?array
    {
        $paths = \glob('/sys/class/thermal/thermal_zone*', \GLOB_NOSORT | \GLOB_BRACE);
        if (!$paths) {
            return null;
        }

        $thermalZoneVals = [];
        foreach ($paths as $path) {
            $sensor = self::parse($path, self::getContents($path.'/type'), self::getContents($path.'/temp'));
            if (null === $sensor) {
                continue;
            }

            $thermalZoneVals[] = $sensor;
        }

        return $thermalZoneVals;
    }

    /**
     * @return array{path: string, name: string, value: float, unit: string}|null
     */
    private static function parse(string $path, ?string $label, ?string $value): ?array
    {
        if (null === $label || null === $value) {
            return null;
        }

        $value = (float) $value;
        $value /= $value > 10000 ? 1000 : 1;

        return [
            'path' => $path,
            'name' => $label,
            'value' => $value,
            'unit' => 'C', // I don't think this is ever going to be in F
        ];
    }
}
