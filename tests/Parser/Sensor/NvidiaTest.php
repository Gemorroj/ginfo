<?php

namespace Ginfo\Tests\Parser\Sensor;

use Ginfo\Parser\Sensor\Nvidia;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NvidiaTest extends TestCase
{
    public static function provideCards(): \Generator
    {
        yield 'two cards' => [
            <<<'TXT'
                GPU 0: NVIDIA GeForce RTX 3080 (UUID: GPU-1234)
                GPU 1: NVIDIA A100-SXM4-40GB (UUID: GPU-abcd)
                TXT,
            [
                ['id' => '0', 'name' => 'NVIDIA GeForce RTX 3080'],
                ['id' => '1', 'name' => 'NVIDIA A100-SXM4-40GB'],
            ],
        ];

        yield 'no cards' => [
            "No devices were found\n",
            null,
        ];
    }

    /**
     * @param array<int, array{id: string, name: string}>|null $expected
     */
    #[DataProvider('provideCards')]
    public function testParseCards(string $output, ?array $expected): void
    {
        $method = new \ReflectionMethod(Nvidia::class, 'parseCards');

        self::assertSame($expected, $method->invoke(null, $output));
    }

    public static function provideCard(): \Generator
    {
        yield 'matching card' => [
            ['0', 'NVIDIA GeForce RTX 3080', <<<'TXT'
                # gpu   pwr gtemp mtemp    sm   mem   enc   dec  mclk  pclk
                # Idx     W     C     C     %     %     %     %   MHz   MHz
                    0    50    40     -     0     0     0     0  7001  1800
                TXT],
            [
                ['path' => null, 'name' => 'NVIDIA GeForce RTX 3080 Power', 'value' => 50.0, 'unit' => 'W'],
                ['path' => null, 'name' => 'NVIDIA GeForce RTX 3080 Temperature', 'value' => 40.0, 'unit' => 'C'],
            ],
        ];

        yield 'id mismatch' => [
            ['1', 'NVIDIA GeForce RTX 3080', "0    50    40\n"],
            [],
        ];

        yield 'no values' => [
            ['0', 'NVIDIA GeForce RTX 3080', "No devices were found\n"],
            [],
        ];
    }

    /**
     * @param array{0: string, 1: string, 2: string}                                         $input
     * @param array<int, array{path: string|null, name: string, value: float, unit: string}> $expected
     */
    #[DataProvider('provideCard')]
    public function testParseCard(array $input, array $expected): void
    {
        $method = new \ReflectionMethod(Nvidia::class, 'parseCard');

        self::assertSame($expected, $method->invoke(null, ...$input));
    }
}
