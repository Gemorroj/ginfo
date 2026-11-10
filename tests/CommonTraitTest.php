<?php

namespace Ginfo\Tests;

use Ginfo\CommonTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CommonTraitTest extends TestCase
{
    public static function providePidStatus(): \Generator
    {
        yield 'full' => [
            "Name:\tnginx\nUmask:\t0022\nState:\tS (sleeping)\nVmPeak:\t  263452 kB\nVmSize:\t  263248 kB\nVmRSS:\t   93456 kB\n",
            ['VmPeak' => 269774848.0, 'VmSize' => 269565952.0],
        ];

        yield 'without memory stats' => [
            "Name:\tnginx\nPid:\t123\n",
            ['VmPeak' => null, 'VmSize' => null],
        ];

        yield 'empty' => [
            '',
            ['VmPeak' => null, 'VmSize' => null],
        ];
    }

    /**
     * @param array{VmPeak: float|null, VmSize: float|null} $expected
     */
    #[DataProvider('providePidStatus')]
    public function testParsePidStatus(string $status, array $expected): void
    {
        $method = new \ReflectionMethod(CommonTraitFixture::class, 'parsePidStatus');

        self::assertSame($expected, $method->invoke(null, $status));
    }

    public static function provideUptime(): \Generator
    {
        yield ['12345', 12345];
        yield ["42\n", 42];
        yield ['', null];
        yield ["\n", null];
    }

    #[DataProvider('provideUptime')]
    public function testParseUptime(string $output, ?int $expected): void
    {
        $method = new \ReflectionMethod(CommonTraitFixture::class, 'parseUptime');

        self::assertSame($expected, $method->invoke(null, $output));
    }

    public static function provideExecutable(): \Generator
    {
        yield 'existing path' => ['sestatus', ['/nonexistent/foo', \PHP_BINARY], \PHP_BINARY];
        yield 'fallback to the bare name' => ['sestatus', ['/nonexistent/foo'], 'sestatus'];
    }

    /**
     * @param string[] $paths
     */
    #[DataProvider('provideExecutable')]
    public function testLocateExecutable(string $name, array $paths, string $expected): void
    {
        $method = new \ReflectionMethod(CommonTraitFixture::class, 'locateExecutable');

        self::assertSame($expected, $method->invoke(null, $name, $paths));
    }
}

final class CommonTraitFixture
{
    use CommonTrait;
}
