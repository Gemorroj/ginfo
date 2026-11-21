<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Parser\WebServer\Frankenphp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FrankenphpParseTest extends TestCase
{
    public static function provideBuildInfo(): \Generator
    {
        yield 'full' => [
            "go\tgo1.27.4\n"
            ."path\tgithub.com/dunglas/frankenphp/caddy/frankenphp\n"
            ."mod\tgithub.com/dunglas/frankenphp/caddy\tv0.0.0\n"
            ."\n"
            ."dep\tgithub.com/dunglas/frankenphp\tv1.13.5\n"
            ."=>\t../\t(devel)\t\n"
            ."dep\tgithub.com/caddyserver/caddy/v2\tv2.11.9\th1:abc\n"
            ."build\t-buildmode=exe\n"
            ."build\t-tags=nobadger,nomysql\n"
            ."not a build info line\n",
            [
                'go' => 'go1.27.4',
                'path' => 'github.com/dunglas/frankenphp/caddy/frankenphp',
                'mod' => "github.com/dunglas/frankenphp/caddy\tv0.0.0",
                'dep' => [
                    "github.com/dunglas/frankenphp\tv1.13.5",
                    "github.com/caddyserver/caddy/v2\tv2.11.9\th1:abc",
                ],
                'build' => ['-buildmode=exe', '-tags=nobadger,nomysql'],
            ],
        ];

        yield 'whitespace' => [
            " go \t go1.27.4 \n path \t /usr/local/bin/frankenphp \n",
            [
                'go' => 'go1.27.4',
                'path' => '/usr/local/bin/frankenphp',
                'mod' => '',
                'dep' => [],
                'build' => [],
            ],
        ];

        yield 'empty' => [
            '',
            [
                'go' => '',
                'path' => '',
                'mod' => '',
                'dep' => [],
                'build' => [],
            ],
        ];
    }

    /**
     * @param array{go: string, path: string, mod: string, dep: string[], build: string[]} $expected
     */
    #[DataProvider('provideBuildInfo')]
    public function testParseBuildInfo(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Frankenphp::class, 'parseBuildInfo');

        self::assertSame($expected, $method->invoke(null, $output));
    }

    public static function provideListModules(): \Generator
    {
        yield 'standard and non-standard' => [
            "admin.api.load\n"
            ."\n"
            ."  Standard modules: 1\n"
            ."\n"
            ."admin.api.frankenphp\n"
            ."frankenphp\n"
            ."http.handlers.php\n"
            ."my_module\n"
            ."my-module\n"
            ."http.handlers.php8\n"
            ."\n"
            ."  Non-standard modules: 6\n",
            [
                'admin.api.load',
                'admin.api.frankenphp',
                'frankenphp',
                'http.handlers.php',
                'my_module',
                'my-module',
                'http.handlers.php8',
            ],
        ];

        yield 'headings and invalid lines only' => [
            "  Standard modules: 1\n\n  Non-standard modules: 0\nnot a module\n",
            [],
        ];

        yield 'empty' => [
            '',
            [],
        ];
    }

    /**
     * @param string[] $expected
     */
    #[DataProvider('provideListModules')]
    public function testParseListModules(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Frankenphp::class, 'parseListModules');

        self::assertSame($expected, $method->invoke(null, $output));
    }

    public static function provideThreads(): \Generator
    {
        $defaultThread = [
            'index' => 0,
            'name' => '',
            'state' => '',
            'is_waiting' => false,
            'is_busy' => false,
            'waiting_since_milliseconds' => 0,
            'current_uri' => '',
            'current_method' => '',
            'request_started_at' => 0,
            'request_count' => 0,
            'memory_usage' => 0,
        ];

        yield 'full' => [
            [
                'ThreadDebugStates' => [
                    [
                        'Index' => 0,
                        'Name' => 'Regular PHP Thread',
                        'State' => 'ready',
                        'IsWaiting' => true,
                        'IsBusy' => false,
                        'WaitingSinceMilliseconds' => 1234,
                        'CurrentURI' => '/foo',
                        'CurrentMethod' => 'GET',
                        'RequestStartedAt' => 1795272459919,
                        'RequestCount' => 42,
                        'MemoryUsage' => 2097152,
                    ],
                ],
                'ReservedThreadCount' => 3,
            ],
            [
                [
                    'index' => 0,
                    'name' => 'Regular PHP Thread',
                    'state' => 'ready',
                    'is_waiting' => true,
                    'is_busy' => false,
                    'waiting_since_milliseconds' => 1234,
                    'current_uri' => '/foo',
                    'current_method' => 'GET',
                    'request_started_at' => 1795272459919,
                    'request_count' => 42,
                    'memory_usage' => 2097152,
                ],
            ],
        ];

        yield 'missing states' => [
            [],
            [],
        ];

        yield 'non-array states' => [
            ['ThreadDebugStates' => 'invalid'],
            [],
        ];

        yield 'mixed states' => [
            ['ThreadDebugStates' => [null, 'invalid', []]],
            [$defaultThread],
        ];

        yield 'missing fields' => [
            ['ThreadDebugStates' => [['Name' => 'worker.php']]],
            [
                \array_replace($defaultThread, ['name' => 'worker.php']),
            ],
        ];

        yield 'casts' => [
            [
                'ThreadDebugStates' => [
                    [
                        'Index' => '2',
                        'Name' => 123,
                        'State' => null,
                        'IsWaiting' => 0,
                        'IsBusy' => 1,
                        'WaitingSinceMilliseconds' => '12',
                        'CurrentURI' => null,
                        'CurrentMethod' => null,
                        'RequestStartedAt' => '1795272459919',
                        'RequestCount' => '42',
                        'MemoryUsage' => '2097152',
                    ],
                ],
            ],
            [
                [
                    'index' => 2,
                    'name' => '123',
                    'state' => '',
                    'is_waiting' => false,
                    'is_busy' => true,
                    'waiting_since_milliseconds' => 12,
                    'current_uri' => '',
                    'current_method' => '',
                    'request_started_at' => 1795272459919,
                    'request_count' => 42,
                    'memory_usage' => 2097152,
                ],
            ],
        ];
    }

    /**
     * @param array<mixed>                               $data
     * @param array<int, array<string, int|string|bool>> $expected
     */
    #[DataProvider('provideThreads')]
    public function testParseThreads(array $data, array $expected): void
    {
        $method = new \ReflectionMethod(Frankenphp::class, 'parseThreads');

        self::assertSame($expected, $method->invoke(null, $data));
    }
}
