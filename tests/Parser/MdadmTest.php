<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Mdadm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MdadmTest extends TestCase
{
    public static function provideMdstat(): \Generator
    {
        yield 'healthy raid1' => [
            <<<'TXT'
                Personalities : [raid1]
                md0 : active raid1 sdb[0] sda[1]
                      1046528 blocks super 1.2 [2/2] [UU]

                unused devices: <none>
                TXT,
            [
                [
                    'device' => '/dev/md0',
                    'status' => 'active',
                    'level' => '1',
                    'drives' => [
                        ['path' => '/dev/sdb', 'state' => 'normal'],
                        ['path' => '/dev/sda', 'state' => 'normal'],
                    ],
                    'size' => 1071644672.0,
                    'count' => ['active' => 2, 'total' => 2],
                    'chart' => 'UU',
                ],
            ],
        ];

        yield 'degraded with failed and spare drives' => [
            <<<'TXT'
                md1 : active raid1 sdc1[0](F) sdd1[1](S)
                      2096128 blocks super 1.2 [2/1] [_U]
                TXT,
            [
                [
                    'device' => '/dev/md1',
                    'status' => 'active',
                    'level' => '1',
                    'drives' => [
                        ['path' => '/dev/sdc1', 'state' => 'failed'],
                        ['path' => '/dev/sdd1', 'state' => 'spare'],
                    ],
                    'size' => 2146435072.0,
                    'count' => ['active' => 1, 'total' => 2],
                    'chart' => '_U',
                ],
            ],
        ];

        yield 'no arrays' => [
            "Personalities : \nunused devices: <none>\n",
            [],
        ];
    }

    /**
     * @param array<int, array{
     *     device: string,
     *     status: string,
     *     level: string,
     *     drives: array<int, array{path: string, state: string}>,
     *     size: float,
     *     count: array{active: int, total: int},
     *     chart: string,
     * }> $expected
     */
    #[DataProvider('provideMdstat')]
    public function testParseMdstat(string $contents, array $expected): void
    {
        $method = new \ReflectionMethod(Mdadm::class, 'parseMdstat');

        self::assertSame($expected, $method->invoke(null, $contents));
    }
}
