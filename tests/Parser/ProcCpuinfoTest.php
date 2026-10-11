<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\ProcCpuinfo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProcCpuinfoTest extends TestCase
{
    public static function provideCpuinfo(): \Generator
    {
        yield 'x86_64: one package, two cores' => [
            <<<CPUINFO
                processor       : 0
                model name      : Intel(R) Core(TM) i7
                physical id     : 0
                cpu cores       : 2
                cache size      : 8192 KB
                flags           : fpu vme lm
                cpu MHz         : 2500.000

                processor       : 1
                model name      : Intel(R) Core(TM) i7
                physical id     : 0
                cpu cores       : 2
                cache size      : 8192 KB
                flags           : fpu vme lm
                cpu MHz         : 2500.000
                CPUINFO,
            [
                'physical' => 1,
                'cores' => 2,
                'virtual' => 2,
                'hyperThreading' => false,
                'processor' => [
                    'model' => 'Intel(R) Core(TM) i7',
                    'speed' => 2500.0,
                    'l2Cache' => 8192 * 1024,
                    'flags' => ['fpu', 'vme', 'lm'],
                    'architecture' => 'x64',
                ],
            ],
        ];

        yield 'arm: no physical id, no cpu cores, no cache size' => [
            <<<CPUINFO
                processor       : 0
                BogoMIPS        : 38.40
                Features        : fp asimd evtstrm aes
                CPU implementer : 0x41
                CPU architecture: 8
                model name      : ARMv8 Processor rev 1

                processor       : 1
                BogoMIPS        : 38.40
                Features        : fp asimd evtstrm aes
                CPU implementer : 0x41
                CPU architecture: 8
                model name      : ARMv8 Processor rev 1
                CPUINFO,
            [
                'physical' => 1,
                'cores' => 2,
                'virtual' => 2,
                'hyperThreading' => false,
                'processor' => [
                    'model' => 'ARMv8 Processor rev 1',
                    'speed' => 0.0,
                    'l2Cache' => null,
                    'flags' => ['fp', 'asimd', 'evtstrm', 'aes'],
                    'architecture' => 'ARM',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideCpuinfo')]
    public function testParse(string $contents, array $expected): void
    {
        $method = new \ReflectionMethod(ProcCpuinfo::class, 'parse');

        $actual = $method->invoke(null, $contents);

        self::assertSame($expected['physical'], $actual['physical']);
        self::assertSame($expected['cores'], $actual['cores']);
        self::assertSame($expected['virtual'], $actual['virtual']);
        self::assertSame($expected['hyperThreading'], $actual['hyperThreading']);
        self::assertCount(1, $actual['processors']);
        self::assertSame($expected['processor'], $actual['processors'][0]);
    }
}
