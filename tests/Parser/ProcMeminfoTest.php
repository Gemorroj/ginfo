<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\ProcMeminfo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProcMeminfoTest extends TestCase
{
    public static function provideMeminfo(): \Generator
    {
        yield 'modern kernel with KReclaimable' => [
            <<<MEMINFO
                MemTotal:       1000000 kB
                MemFree:         200000 kB
                MemAvailable:    500000 kB
                Buffers:          10000 kB
                Cached:          300000 kB
                Shmem:            50000 kB
                KReclaimable:     20000 kB
                SwapTotal:       100000 kB
                SwapFree:         40000 kB
                MEMINFO,
            [
                'total' => 1000000 * 1024,
                'used' => (1000000 - 200000 - (300000 - 50000 + 10000 + 20000)) * 1024,
                'free' => 200000 * 1024,
                'available' => 500000 * 1024,
                'shared' => 50000 * 1024,
                'buffers' => 10000 * 1024,
                'cached' => (300000 - 50000 + 10000 + 20000) * 1024,
                'swapTotal' => 100000 * 1024,
                'swapUsed' => (100000 - 40000) * 1024,
                'swapFree' => 40000 * 1024,
            ],
        ];

        yield 'old kernel without KReclaimable' => [
            <<<MEMINFO
                MemTotal:       1000000 kB
                MemFree:         200000 kB
                MemAvailable:    500000 kB
                Buffers:          10000 kB
                Cached:          300000 kB
                Shmem:            50000 kB
                SwapTotal:            0 kB
                SwapFree:             0 kB
                MEMINFO,
            [
                'total' => 1000000 * 1024,
                'used' => (1000000 - 200000 - (300000 - 50000 + 10000)) * 1024,
                'free' => 200000 * 1024,
                'available' => 500000 * 1024,
                'shared' => 50000 * 1024,
                'buffers' => 10000 * 1024,
                'cached' => (300000 - 50000 + 10000) * 1024,
                'swapTotal' => 0 * 1024,
                'swapUsed' => 0 * 1024,
                'swapFree' => 0 * 1024,
            ],
        ];

        yield 'without MemAvailable falls back to MemFree' => [
            <<<MEMINFO
                MemTotal:       1000000 kB
                MemFree:         200000 kB
                Buffers:          10000 kB
                Cached:          300000 kB
                Shmem:            50000 kB
                SwapTotal:            0 kB
                SwapFree:             0 kB
                MEMINFO,
            [
                'available' => 200000 * 1024,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideMeminfo')]
    public function testParse(string $contents, array $expected): void
    {
        $method = new \ReflectionMethod(ProcMeminfo::class, 'parse');

        $actual = $method->invoke(null, $contents);

        foreach ($expected as $key => $value) {
            self::assertSame((float) $value, $actual[$key], 'key '.$key);
        }
    }
}
