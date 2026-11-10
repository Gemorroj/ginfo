<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\ThermalZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ThermalZoneTest extends TestCase
{
    public static function provideSensor(): \Generator
    {
        yield 'millidegrees' => [
            ['/sys/class/thermal/thermal_zone0', 'x86_pkg_temp', '42000'],
            ['path' => '/sys/class/thermal/thermal_zone0', 'name' => 'x86_pkg_temp', 'value' => 42.0, 'unit' => 'C'],
        ];

        yield 'degrees' => [
            ['/sys/class/thermal/thermal_zone1', 'acpitz', '42'],
            ['path' => '/sys/class/thermal/thermal_zone1', 'name' => 'acpitz', 'value' => 42.0, 'unit' => 'C'],
        ];

        yield 'boundary is not divided' => [
            ['/sys/class/thermal/thermal_zone2', 'acpitz', '10000'],
            ['path' => '/sys/class/thermal/thermal_zone2', 'name' => 'acpitz', 'value' => 10000.0, 'unit' => 'C'],
        ];

        yield 'missing label' => [
            ['/sys/class/thermal/thermal_zone3', null, '42000'],
            null,
        ];

        yield 'missing value' => [
            ['/sys/class/thermal/thermal_zone4', 'acpitz', null],
            null,
        ];
    }

    /**
     * @param array{0: string, 1: string|null, 2: string|null}                   $input
     * @param array{path: string, name: string, value: float, unit: string}|null $expected
     */
    #[DataProvider('provideSensor')]
    public function testParse(array $input, ?array $expected): void
    {
        $method = new \ReflectionMethod(ThermalZone::class, 'parse');

        self::assertSame($expected, $method->invoke(null, ...$input));
    }
}
