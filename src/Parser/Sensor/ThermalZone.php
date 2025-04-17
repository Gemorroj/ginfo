<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\Common;
use Ginfo\Parser\ParserInterface;

final readonly class ThermalZone implements ParserInterface
{
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

            $label = Common::getContents($labelPath);
            $value = Common::getContents($valuePath);

            if (null === $label || null === $value) {
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
