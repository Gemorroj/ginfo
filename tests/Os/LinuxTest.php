<?php

namespace Ginfo\Tests\Os;

use Ginfo\Info\Disk\Drive\Partition;
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

    public static function provideUptime(): \Generator
    {
        yield ['12345.67 99999.99', 12345];
        yield ["42.5 100.0\n", 42];
        yield ['', null];
        yield ["  \n", null];
    }

    #[DataProvider('provideUptime')]
    public function testParseUptime(string $content, ?int $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseUptime');

        self::assertSame($expected, $method->invoke(null, $content));
    }

    public static function provideLoad(): \Generator
    {
        yield ['0.11 0.17 0.18 1/234 5678', [0.11, 0.17, 0.18]];
        yield ['1.00 2.00 3.00', [1.0, 2.0, 3.0]];
        yield ['0.5 0.6', null];
        yield ['', null];
        yield ['a b c', null];
    }

    /**
     * @param float[]|null $expected
     */
    #[DataProvider('provideLoad')]
    public function testParseLoad(string $content, ?array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseLoad');

        self::assertSame($expected, $method->invoke(null, $content));
    }

    public static function provideModel(): \Generator
    {
        yield 'vendor and product' => [
            ['B450 TOMAHAWK', 'Micro-Star International Co., Ltd.', 'MS-7B89'],
            'MS-7B89 (Micro-Star International Co., Ltd. B450 TOMAHAWK)',
        ];

        yield 'product is already in the name' => [
            ['Dell Inc. XPS 13 9370', 'Dell Inc.', 'XPS 13 9370'],
            'Dell Inc. XPS 13 9370',
        ];

        yield 'placeholder product' => [
            ['B450 TOMAHAWK', 'MSI', 'Filled by O.E.M.'],
            'MSI B450 TOMAHAWK',
        ];

        yield 'without vendor and product' => [
            ['B450 TOMAHAWK', null, null],
            'B450 TOMAHAWK',
        ];

        yield 'without name' => [
            [null, 'MSI', 'MS-7B89'],
            null,
        ];

        yield 'empty name' => [
            ['', 'MSI', 'MS-7B89'],
            null,
        ];
    }

    /**
     * @param array{0: string|null, 1: string|null, 2: string|null} $input
     */
    #[DataProvider('provideModel')]
    public function testParseModel(array $input, ?string $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseModel');

        self::assertSame($expected, $method->invoke(null, ...$input));
    }

    public static function provideMountLines(): \Generator
    {
        yield 'common mounts' => [
            <<<'TXT'
                /dev/sda1 / ext4 rw,relatime,errors=remount-ro 0 0
                proc /proc proc rw,nosuid,nodev,noexec,relatime 0 0
                /dev/sdb1 /mnt/my\040disk ext4 rw,relatime 0 0
                TXT,
            [
                ['device' => '/dev/sda1', 'mount' => '/', 'fstype' => 'ext4', 'options' => ['rw', 'relatime', 'errors=remount-ro']],
                ['device' => 'proc', 'mount' => '/proc', 'fstype' => 'proc', 'options' => ['rw', 'nosuid', 'nodev', 'noexec', 'relatime']],
                ['device' => '/dev/sdb1', 'mount' => '/mnt/my disk', 'fstype' => 'ext4', 'options' => ['rw', 'relatime']],
            ],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<int, array{device: string, mount: string, fstype: string, options: string[]}>|null $expected
     */
    #[DataProvider('provideMountLines')]
    public function testParseMountLines(string $contents, ?array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseMountLines');

        self::assertSame($expected, $method->invoke(null, $contents));
    }

    public static function providePartitions(): \Generator
    {
        yield 'sata and nvme partitions' => [
            <<<'TXT'
                major minor  #blocks  name

                   8        0  976762584 sda
                   8        1     524288 sda1
                   8        2  976237568 sda2
                 259        0  500107862 nvme0n1
                 259        1     524288 nvme0n1p1
                TXT,
            [
                'sda' => [
                    new Partition(536870912.0, 'sda1'),
                    new Partition(999667269632.0, 'sda2'),
                ],
                'nvme0n1' => [
                    new Partition(536870912.0, 'nvme0n1p1'),
                ],
            ],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<string, Partition[]> $expected
     */
    #[DataProvider('providePartitions')]
    public function testParsePartitions(string $contents, array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parsePartitions');

        self::assertEquals($expected, $method->invoke(null, $contents));
    }
}
