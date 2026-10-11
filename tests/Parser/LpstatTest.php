<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Lpstat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LpstatTest extends TestCase
{
    public static function provideLpstat(): \Generator
    {
        yield 'enabled and disabled' => [
            "printer HP_LaserJet is idle.  enabled since Mon 01 Jan 2024 10:00:00 AM UTC\nprinter Old_Printer disabled since Mon 01 Jan 2024 10:00:00 AM UTC -\n",
            [
                ['name' => 'HP LaserJet', 'enabled' => true],
                ['name' => 'Old Printer', 'enabled' => false],
            ],
        ];

        yield 'no printers' => [
            "lpstat: No destinations added.\n",
            [],
        ];
    }

    /**
     * @param array<int, array{name: string, enabled: bool}> $expected
     */
    #[DataProvider('provideLpstat')]
    public function testParse(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Lpstat::class, 'parse');

        self::assertSame($expected, $method->invoke(null, $output));
    }
}
