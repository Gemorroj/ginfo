<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\Sensors;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SensorsTest extends TestCase
{
    public static function provideSensorLineStrings(): \Generator
    {
        yield ['acpitz-virtual-0', false];
        yield ['Adapter: Virtual device', false];
        yield ['                       (crit = +105.0°C, hyst =  +5.0°C)', false];
        yield ['Core0 Temp: +26.0°C', true];
        yield ['Vcore: +1.32 V (min = +0.00 V, max = +1.74 V)', true];
        yield ['cpu0_vid: +0.000 V', true];
        yield ['temp2: +63.5°C (high = +65.0°C, hyst = +65.0°C) sensor = diode', true];
        yield ['fan3: 0 RPM (min = 1506 RPM, div = 128) ALARM', true];
    }

    public static function provideSensorStrings(): \Generator
    {
        yield ['temp1: +60.0°C (crit = +90.0°C)', [
            'path' => null,
            'name' => 'temp1',
            'value' => '+60.0',
            'unit' => 'C',
        ]];
        yield ['Core 0:       +58.0°C  (high = +84.0°C, crit = +100.0°C)', [
            'path' => null,
            'name' => 'Core 0',
            'value' => '+58.0',
            'unit' => 'C',
        ]];
        yield ['Vcore: +1.32 V (min = +0.00 V, max = +1.74 V)', [
            'path' => null,
            'name' => 'Vcore',
            'value' => '+1.32',
            'unit' => 'V',
        ]];
        yield ['fan3: 0 RPM (min = 1506 RPM, div = 128) ALARM', [
            'path' => null,
            'name' => 'fan3',
            'value' => '0',
            'unit' => 'RPM',
        ]];
        yield ['cpu0_vid: +0.000 V', [
            'path' => null,
            'name' => 'cpu0_vid',
            'value' => '+0.000',
            'unit' => 'V',
        ]];
    }

    #[DataProvider('provideSensorLineStrings')]
    public function testIsSensorLine(string $data, bool $expected): void
    {
        $method = new \ReflectionMethod(Sensors::class, 'isSensorLine');

        $actual = $method->invoke(null, $data);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param array{path: string|null, name: string, value: string, unit: string} $expected
     */
    #[DataProvider('provideSensorStrings')]
    public function testParseSensor(string $data, array $expected): void
    {
        $method = new \ReflectionMethod(Sensors::class, 'parseSensor');

        $actual = $method->invoke(null, $data);

        self::assertEquals($expected, $actual);
    }
}
