<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Who;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WhoTest extends TestCase
{
    public static function provideWho(): \Generator
    {
        yield 'gnu' => [
            "root     pts/0        2024-01-01 10:00 (:0)\ngemorroj pts/1        2024-01-01 11:00 (10.0.0.1)\n",
            ['root', 'gemorroj'],
        ];

        yield 'busybox with header' => [
            "USER     TTY      TIME\nroot     pts/0    10:00\n",
            ['root'],
        ];

        yield 'gnu with boot footer' => [
            "root     pts/0        2024-01-01 10:00\n         system boot  2024-01-01 09:00\ngemorroj pts/1        2024-01-01 11:00\n",
            ['root', 'gemorroj'],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param string[] $expected
     */
    #[DataProvider('provideWho')]
    public function testParse(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Who::class, 'parse');

        self::assertSame($expected, $method->invoke(null, $output));
    }
}
