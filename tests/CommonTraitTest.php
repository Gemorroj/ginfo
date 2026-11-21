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

    public static function provideSystemUptime(): \Generator
    {
        yield 'normal' => ['52134.57 1241771.72', 52134.57];
        yield 'integer' => ['42', 42.0];
        yield 'whitespace' => [" 42.5\n", 42.5];
        yield 'empty' => ['', null];
        yield 'invalid' => ['not-a-number', null];
    }

    #[DataProvider('provideSystemUptime')]
    public function testParseSystemUptime(string $uptime, ?float $expected): void
    {
        $method = new \ReflectionMethod(CommonTraitFixture::class, 'parseSystemUptime');

        self::assertSame($expected, $method->invoke(null, $uptime));
    }

    public static function providePidStatStartTime(): \Generator
    {
        yield 'simple' => [
            "8 (frankenphp) S 1 1 1 0 -1 4194304 77 0 0 0 0 0 0 0 20 0 1 0 5218282 2650112 364\n",
            5218282.0,
        ];

        yield 'comm with spaces' => [
            "8 (my process) S 1 1 1 0 -1 4194304 77 0 0 0 0 0 0 0 20 0 1 0 5221735 2650112 364\n",
            5221735.0,
        ];

        yield 'comm with parentheses' => [
            "8 (my) process) S 1 1 1 0 -1 4194304 77 0 0 0 0 0 0 0 20 0 1 0 5221736 2650112 364\n",
            5221736.0,
        ];

        yield 'empty' => ['', null];
        yield 'invalid' => ['8 frankenphp S 1 2 3', null];
        yield 'too few fields' => ['8 (frankenphp) S 1 2 3', null];
    }

    #[DataProvider('providePidStatStartTime')]
    public function testParsePidStatStartTime(string $stat, ?float $expected): void
    {
        $method = new \ReflectionMethod(CommonTraitFixture::class, 'parsePidStatStartTime');

        self::assertSame($expected, $method->invoke(null, $stat));
    }

    public static function provideProcessUptime(): \Generator
    {
        $stat = "8 (frankenphp) S 1 1 1 0 -1 4194304 77 0 0 0 0 0 0 0 20 0 1 0 5218282 2650112 364\n";

        yield 'normal' => [$stat, 52184.82, 100, 2];
        yield 'truncate' => [$stat, 52184.92, 100, 2];
        yield 'clamp negative' => [$stat, 52182.0, 100, 0];
        yield 'invalid stat' => ['broken', 52184.82, 100, null];
        yield 'invalid clk tck' => [$stat, 52184.82, 0, null];
    }

    #[DataProvider('provideProcessUptime')]
    public function testParseProcessUptime(string $stat, float $systemUptime, int $clkTck, ?int $expected): void
    {
        $method = new \ReflectionMethod(CommonTraitFixture::class, 'parseProcessUptime');

        self::assertSame($expected, $method->invoke(null, $stat, $systemUptime, $clkTck));
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
