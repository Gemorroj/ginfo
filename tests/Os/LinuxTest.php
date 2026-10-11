<?php

namespace Ginfo\Tests\Os;

use Ginfo\Os\Linux;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LinuxTest extends TestCase
{
    public static function providePercent(): \Generator
    {
        yield [50.0, 200.0, 25.0];
        yield [1.0, 3.0, 33.33];
        yield [200.0, 200.0, 100.0];
        yield [null, 100.0, null];
        yield [10.0, null, null];
        yield [10.0, 0.0, null];
    }

    #[DataProvider('providePercent')]
    public function testPercent(?float $part, ?float $total, ?float $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'percent');

        self::assertSame($expected, $method->invoke(null, $part, $total));
    }

    public static function provideSpeed(): \Generator
    {
        yield ['1000', 1000000000.0];
        yield ['100', 100000000.0];
        yield ['4294967295', null];
        yield ['-1', null];
        yield ['0', null];
        yield ['abc', null];
        yield ['', null];
        yield [null, null];
    }

    #[DataProvider('provideSpeed')]
    public function testParseSpeed(?string $raw, ?float $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseSpeed');

        self::assertSame($expected, $method->invoke(null, $raw));
    }

    public static function provideSoundCards(): \Generator
    {
        yield 'two cards' => [
            [
                ' 0 [PCH            ]: HDA-Intel - HDA Intel PCH',
                '                      HDA Intel PCH at 0xf7f10000 irq 47',
                ' 1 [HDMI           ]: HDA-Intel - HDA HDMI',
                '                      HDA HDMI at 0xf7f20000 irq 48',
            ],
            [
                ['vendor' => 'HDA Intel PCH', 'name' => 'HDA-Intel - HDA Intel PCH'],
                ['vendor' => 'HDA HDMI', 'name' => 'HDA-Intel - HDA HDMI'],
            ],
        ];

        yield 'no soundcards single line' => [
            ['--- no soundcards ---'],
            [],
        ];

        yield 'dangling last line is ignored' => [
            [
                ' 0 [PCH            ]: HDA-Intel - HDA Intel PCH',
                '                      HDA Intel PCH at 0xf7f10000 irq 47',
                'dangling',
            ],
            [
                ['vendor' => 'HDA Intel PCH', 'name' => 'HDA-Intel - HDA Intel PCH'],
            ],
        ];
    }

    /**
     * @param string[]                                        $lines
     * @param array<int, array{vendor: string, name: string}> $expected
     */
    #[DataProvider('provideSoundCards')]
    public function testParseSoundCards(array $lines, array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseSoundCards');

        $cards = $method->invoke(null, $lines);

        self::assertCount(\count($expected), $cards);
        foreach ($expected as $i => $card) {
            self::assertSame($card['vendor'], $cards[$i]->getVendor());
            self::assertSame($card['name'], $cards[$i]->getName());
        }
    }
}
