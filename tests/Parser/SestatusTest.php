<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Sestatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SestatusTest extends TestCase
{
    public static function provideOutput(): \Generator
    {
        yield 'enforcing' => [
            <<<'TXT'
                SELinux status:                 enabled
                SELinuxfs mount:                /sys/fs/selinux
                SELinux root directory:         /etc/selinux
                Loaded policy name:             targeted
                Current mode:                   enforcing
                Mode from config file:          enforcing
                Policy MLS status:              enabled
                Policy deny_unknown status:     allowed
                Max kernel policy version:      33
                TXT,
            ['enabled' => true, 'mode' => 'enforcing', 'policy' => 'targeted'],
        ];

        yield 'permissive' => [
            <<<'TXT'
                SELinux status:                 enabled
                Loaded policy name:             targeted
                Current mode:                   permissive
                TXT,
            ['enabled' => true, 'mode' => 'permissive', 'policy' => 'targeted'],
        ];

        yield 'disabled' => [
            "SELinux status:                 disabled\n",
            ['enabled' => false, 'mode' => null, 'policy' => null],
        ];

        yield 'empty' => [
            '',
            ['enabled' => false, 'mode' => null, 'policy' => null],
        ];
    }

    /**
     * @param array{enabled: bool, mode: string|null, policy: string|null} $expected
     */
    #[DataProvider('provideOutput')]
    public function testParse(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Sestatus::class, 'parse');

        self::assertSame($expected, $method->invoke(null, $output));
    }
}
