<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\Mbmon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MbmonTest extends TestCase
{
    public static function provideSockData(): \Generator
    {
        yield 'temperatures, voltages and fans' => [
            "Temp0 : +36.0\nVcore0 : +1.52\nFan0 : 3240\n",
            [
                ['path' => null, 'name' => 'Temp0', 'value' => 36.0, 'unit' => 'C'],
                ['path' => null, 'name' => 'Vcore0', 'value' => 1.52, 'unit' => 'C'],
                ['path' => null, 'name' => 'Fan0', 'value' => 3240.0, 'unit' => 'C'],
            ],
        ];

        yield 'negative value' => [
            "Temp1: -5.0\n",
            [
                ['path' => null, 'name' => 'Temp1', 'value' => -5.0, 'unit' => 'C'],
            ],
        ];

        yield 'lines without a value are ignored' => [
            "mbmon: no sensors found\nTemp2 : n/a\n",
            [],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<int, array{path: string|null, name: string, value: float, unit: string}> $expected
     */
    #[DataProvider('provideSockData')]
    public function testParseSockData(string $data, array $expected): void
    {
        $method = new \ReflectionMethod(Mbmon::class, 'parseSockData');

        self::assertSame($expected, $method->invoke(null, $data));
    }
}
