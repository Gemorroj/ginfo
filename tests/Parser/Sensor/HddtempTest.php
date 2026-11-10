<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\Hddtemp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HddtempTest extends TestCase
{
    public static function provideSockData(): \Generator
    {
        yield 'single drive' => [
            '|/dev/sda|Samsung SSD 860|35|C|',
            [
                ['path' => '/dev/sda', 'name' => 'Samsung SSD 860', 'value' => 35.0, 'unit' => 'C'],
            ],
        ];

        yield 'multiple drives and uppercased unit' => [
            '|/dev/sda|Samsung SSD 860|35|C||/dev/sdb|WDC WD10|42|c|',
            [
                ['path' => '/dev/sda', 'name' => 'Samsung SSD 860', 'value' => 35.0, 'unit' => 'C'],
                ['path' => '/dev/sdb', 'name' => 'WDC WD10', 'value' => 42.0, 'unit' => 'C'],
            ],
        ];

        yield 'garbled drive is skipped' => [
            '|/dev/sdb|Some SSD|UNK|C|',
            [],
        ];

        yield 'malformed chunk is skipped' => [
            '|/dev/sda|broken||/dev/sdb|WDC WD10|42|C|',
            [
                ['path' => '/dev/sdb', 'name' => 'WDC WD10', 'value' => 42.0, 'unit' => 'C'],
            ],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<int, array{path: string, name: string, value: float, unit: string}> $expected
     */
    #[DataProvider('provideSockData')]
    public function testParseSockData(string $data, array $expected): void
    {
        $method = new \ReflectionMethod(Hddtemp::class, 'parseSockData');

        self::assertSame($expected, $method->invoke(null, $data));
    }
}
