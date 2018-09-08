<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Common;
use Ginfo\Parsers\Parser;

class ThermalZone implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function work(): ?array
    {
        $paths = \glob('/sys/class/thermal/thermal_zone*', \GLOB_NOSORT | \GLOB_BRACE);
        if (false === $paths) {
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
