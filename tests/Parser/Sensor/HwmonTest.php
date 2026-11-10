<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\Hwmon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HwmonTest extends TestCase
{
    public static function provideSensor(): \Generator
    {
        yield 'temperature with label and driver' => [
            ['temp1_input', '42000', 'Core 0', null, 'coretemp'],
            ['path' => null, 'name' => 'Core 0 (coretemp)', 'value' => 42.0, 'unit' => 'C'],
        ];

        yield 'temperature in celsius' => [
            ['temp1_input', '42', 'Core 0', null, null],
            ['path' => null, 'name' => 'Core 0', 'value' => 42.0, 'unit' => 'C'],
        ];

        yield 'temperature boundary is not divided' => [
            ['temp1_input', '10000', 'Core 0', null, null],
            ['path' => null, 'name' => 'Core 0', 'value' => 10000.0, 'unit' => 'C'],
        ];

        yield 'device temperature from model' => [
            ['temp1_input', '35000', null, 'Samsung SSD 860', 'drivetemp'],
            ['path' => null, 'name' => 'Samsung SSD 860 (drivetemp)', 'value' => 35.0, 'unit' => 'C'],
        ];

        yield 'fan' => [
            ['fan1_input', '1200', null, null, 'it8728'],
            ['path' => null, 'name' => 'fan1 (it8728)', 'value' => 1200.0, 'unit' => 'RPM'],
        ];

        yield 'voltage with label' => [
            ['in0_input', '1520', 'Vcore', null, 'it8728'],
            ['path' => null, 'name' => 'Vcore (it8728)', 'value' => 1.52, 'unit' => 'V'],
        ];

        yield 'voltage without label' => [
            ['in0_input', '950', null, null, null],
            ['path' => null, 'name' => 'in0', 'value' => 0.95, 'unit' => 'V'],
        ];

        yield 'unsupported input' => [
            ['pwm1_input', '100', null, null, null],
            null,
        ];
    }

    /**
     * @param array{0: string, 1: string, 2: string|null, 3: string|null, 4: string|null} $input
     * @param array{path: string|null, name: string, value: float, unit: string}|null     $expected
     */
    #[DataProvider('provideSensor')]
    public function testParseSensor(array $input, ?array $expected): void
    {
        $method = new \ReflectionMethod(Hwmon::class, 'parseSensor');

        self::assertSame($expected, $method->invoke(null, ...$input));
    }
}
