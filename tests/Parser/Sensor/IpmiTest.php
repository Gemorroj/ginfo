<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\Ipmi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IpmiTest extends TestCase
{
    public static function provideOutput(): \Generator
    {
        yield 'temperatures and voltages' => [
            <<<'TXT'
                CPU Temp         | 41 degrees C      | ok
                System Temp      | 28 degrees C      | ok
                12V              | 12.10 Volts       | ok
                FAN1             | 3200 RPM          | ok
                CPU Temp         | 41 degrees C      | ns
                TXT,
            [
                ['path' => null, 'name' => 'CPU Temp', 'value' => 41.0, 'unit' => 'C'],
                ['path' => null, 'name' => 'System Temp', 'value' => 28.0, 'unit' => 'C'],
                ['path' => null, 'name' => '12V', 'value' => 12.1, 'unit' => 'V'],
            ],
        ];

        yield 'no matching sensors' => [
            "FAN1             | 3200 RPM          | ok\n",
            null,
        ];

        yield 'empty' => [
            '',
            null,
        ];
    }

    /**
     * @param array<int, array{path: string|null, name: string, value: float, unit: string}>|null $expected
     */
    #[DataProvider('provideOutput')]
    public function testParse(string $output, ?array $expected): void
    {
        $method = new \ReflectionMethod(Ipmi::class, 'parse');

        self::assertSame($expected, $method->invoke(null, $output));
    }
}
