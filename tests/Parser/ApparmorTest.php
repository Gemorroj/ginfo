<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Apparmor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApparmorTest extends TestCase
{
    public static function provideJson(): \Generator
    {
        yield 'profiles and processes' => [
            [
                'version' => '4.0.1',
                'profiles' => [
                    '/usr/bin/foo' => 'enforce',
                    '/usr/bin/bar' => 'complain',
                ],
                'processes' => [
                    '/usr/sbin/cupsd' => [
                        ['profile' => '/usr/sbin/cupsd', 'pid' => '1234', 'status' => 'enforce'],
                        ['profile' => '/usr/sbin/cupsd//hat', 'pid' => '5678', 'status' => 'enforce'],
                    ],
                ],
            ],
            [
                'enabled' => true,
                'profiles' => [
                    '/usr/bin/foo' => 'enforce',
                    '/usr/bin/bar' => 'complain',
                ],
                'processes' => [
                    ['executable' => '/usr/sbin/cupsd', 'profile' => '/usr/sbin/cupsd', 'pid' => 1234, 'status' => 'enforce'],
                    ['executable' => '/usr/sbin/cupsd', 'profile' => '/usr/sbin/cupsd//hat', 'pid' => 5678, 'status' => 'enforce'],
                ],
            ],
        ];

        yield 'invalid entries are skipped' => [
            [
                'profiles' => [
                    '/usr/bin/foo' => 'enforce',
                    '/usr/bin/bar' => ['nested'],
                ],
                'processes' => [
                    '/usr/bin/foo' => 'invalid',
                ],
            ],
            [
                'enabled' => true,
                'profiles' => [
                    '/usr/bin/foo' => 'enforce',
                ],
                'processes' => [],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideJson')]
    public function testParseJson(array $input, array $expected): void
    {
        $method = new \ReflectionMethod(Apparmor::class, 'parseJson');

        self::assertSame($expected, $method->invoke(null, $input));
    }

    public static function provideText(): \Generator
    {
        yield 'profiles and processes' => [
            <<<'TXT'
                apparmor module is loaded.
                2 profiles are loaded.
                1 profiles are in enforce mode.
                   /usr/bin/foo
                1 profiles are in complain mode.
                   /usr/bin/bar
                0 profiles are in kill mode.
                0 profiles are in unconfined mode.
                1 processes have profiles defined.
                1 processes are in enforce mode.
                   /usr/sbin/cupsd (1234)
                0 processes are in complain mode.
                0 processes are unconfined but have a profile defined.
                TXT,
            [
                'enabled' => true,
                'profiles' => [
                    '/usr/bin/foo' => 'enforce',
                    '/usr/bin/bar' => 'complain',
                ],
                'processes' => [
                    ['executable' => '/usr/sbin/cupsd', 'profile' => '/usr/sbin/cupsd', 'pid' => 1234, 'status' => 'enforce'],
                ],
            ],
        ];

        yield 'process with another profile' => [
            <<<'TXT'
                apparmor module is loaded.
                1 processes have profiles defined.
                1 processes are in enforce mode.
                   /usr/bin/other (4321) /usr/sbin/cupsd
                TXT,
            [
                'enabled' => true,
                'profiles' => [],
                'processes' => [
                    ['executable' => '/usr/bin/other', 'profile' => '/usr/sbin/cupsd', 'pid' => 4321, 'status' => 'enforce'],
                ],
            ],
        ];

        yield 'module is not loaded' => [
            "apparmor module is not loaded.\n",
            [
                'enabled' => false,
                'profiles' => [],
                'processes' => [],
            ],
        ];

        yield 'filesystem is not mounted' => [
            "apparmor filesystem is not mounted.\n",
            [
                'enabled' => false,
                'profiles' => [],
                'processes' => [],
            ],
        ];

        yield 'module without policy' => [
            "apparmor module is loaded.\n0 profiles are loaded.\n0 processes have profiles defined.\n",
            [
                'enabled' => true,
                'profiles' => [],
                'processes' => [],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideText')]
    public function testParseText(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Apparmor::class, 'parseText');

        self::assertSame($expected, $method->invoke(null, $output));
    }

    public function testRealOutput(): void
    {
        $json = \json_decode((string) \file_get_contents(__DIR__.'/../Fixtures/apparmor-status.json'), true, 512, \JSON_THROW_ON_ERROR);

        $parseJson = new \ReflectionMethod(Apparmor::class, 'parseJson');
        $parseText = new \ReflectionMethod(Apparmor::class, 'parseText');

        $fromJson = $parseJson->invoke(null, $json);
        $fromText = $parseText->invoke(null, (string) \file_get_contents(__DIR__.'/../Fixtures/apparmor-status.txt'));

        self::assertTrue($fromJson['enabled']);
        self::assertCount(106, $fromJson['profiles']);
        self::assertSame('enforce', $fromJson['profiles']['/usr/bin/man']);
        self::assertSame('complain', $fromJson['profiles']['transmission-cli']);
        self::assertSame('unconfined', $fromJson['profiles']['1password']);
        self::assertSame([
            ['executable' => '/usr/sbin/mysqld', 'profile' => '/usr/sbin/mysqld', 'pid' => 1004, 'status' => 'enforce'],
        ], $fromJson['processes']);

        // both aa-status formats must produce the same data
        self::assertEquals($fromJson, $fromText);
    }
}
