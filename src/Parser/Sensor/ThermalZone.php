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
            $labelPath = $path.'/type';
            $valuePath = $path.'/temp';

            $label = self::getContents($labelPath);
            if (null === $label) {
                continue;
            }
            $value = self::getContents($valuePath);
            if (null === $value) {
                continue;
            }

            $value /= $value > 10000 ? 1000 : 1;

            $thermalZoneVals[] = [
                'path' => $path,
                'name' => $label,
                'value' => $value,
                'unit' => 'C', // I don't think this is ever going to be in F
            ];
        }

        return $thermalZoneVals;
    }
}
