<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Apcaccess;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApcaccessTest extends TestCase
{
    public static function provideApcaccess(): \Generator
    {
        yield 'typical status' => [
            <<<OUT
                APC      : 001,036,0843
                UPSNAME  : myups
                MODEL    : Smart-UPS 1500
                STATUS   : ONLINE
                LINEV    : 230.0 Volts
                LOADPCT  : 21.0 Percent
                BCHARGE  : 100.0 Percent
                TIMELEFT : 23.0 Minutes
                OUT,
            [
                'name' => 'myups',
                'model' => 'Smart-UPS 1500',
                'batteryVolts' => 0.0,
                'batteryCharge' => 100.0,
                'timeLeft' => 23 * 60,
                'currentLoad' => 21.0,
                'status' => 'ONLINE',
            ],
        ];

        yield 'minimal with battery volts' => [
            "UPSNAME : u\nBATTV : 13.6 Volts\n",
            [
                'name' => 'u',
                'model' => '',
                'batteryVolts' => 13.6,
                'batteryCharge' => 0.0,
                'timeLeft' => 0,
                'currentLoad' => 0.0,
                'status' => '',
            ],
        ];

        yield 'error output' => [
            "Error: Unable to connect\n",
            null,
        ];

        yield 'without UPSNAME' => [
            "MODEL : some\n",
            null,
        ];
    }

    /**
     * @param array<string, mixed>|null $expected
     */
    #[DataProvider('provideApcaccess')]
    public function testParse(string $output, ?array $expected): void
    {
        $method = new \ReflectionMethod(Apcaccess::class, 'parse');

        self::assertSame($expected, $method->invoke(null, $output));
    }
}
