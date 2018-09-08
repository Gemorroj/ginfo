<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Common;
use Ginfo\Parsers\Parser;

class Hwmon implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function work(): ?array
    {
        $paths = \glob('/sys/class/hwmon/hwmon*/{,device/}*_input', \GLOB_NOSORT | \GLOB_BRACE);
        if (false === $paths) {
            return null;
        }

        $hwmonVals = [];
        foreach ($paths as $path) {
            $initPath = \rtrim($path, 'input');
            $value = Common::getContents($path);
            $base = \basename($path);
            $labelPath = $initPath.'label';
            $driverName = Common::getContents(\dirname($path).'/name');

            // Temperatures
            if (0 === \mb_strpos($base, 'temp') && \is_file($labelPath)) {
                $label = Common::getContents($labelPath);
                $value /= $value > 10000 ? 1000 : 1;
                $unit = 'C'; // I don't think this is ever going to be in F
            } // Fan RPMs
            elseif (\preg_match('/^fan(\d+)_/', $base, $m)) {
                $label = 'fan'.$m[1];
                $unit = 'RPM';
            } // Volts
            elseif (\preg_match('/^in(\d+)_/', $base, $m)) {
                $unit = 'V';
                $value /= 1000;
                $label = Common::getContents($labelPath) ?: 'in'.$m[1];
            } else {
                continue;
            }

            // Append values
            $hwmonVals[] = [
                'path' => null,
                'name' => $label.($driverName ? ' ('.$driverName.')' : ''),
                'value' => $value,
                'unit' => $unit,
            ];
        }

        return $hwmonVals;
    }
}
