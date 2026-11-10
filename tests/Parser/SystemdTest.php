<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Systemd;
use PHPUnit\Framework\TestCase;

final class SystemdTest extends TestCase
{
    public function testParseList(): void
    {
        $list = <<<'TXT'
              UNIT                     LOAD   ACTIVE SUB     DESCRIPTION
              apache2.service          loaded active running The Apache HTTP Server
            ● systemd-timesyncd.service loaded active running Network Time Synchronization
              broken.service           error  inactive dead  Broken service

            LOAD   = Reflects whether the unit definition was properly loaded.
            ACTIVE = The high-level unit activation state, i.e. generalization of SUB.
            SUB    = The low-level unit activation state, values depend on unit type.

            3 loaded units listed.
            TXT;

        $method = new \ReflectionMethod(Systemd::class, 'parseList');

        self::assertSame([
            [
                'name' => 'apache2.service',
                'loaded' => true,
                'started' => true,
                'state' => 'running',
                'description' => 'The Apache HTTP Server',
            ],
            [
                'name' => 'systemd-timesyncd.service',
                'loaded' => true,
                'started' => true,
                'state' => 'running',
                'description' => 'Network Time Synchronization',
            ],
            [
                'name' => 'broken.service',
                'loaded' => false,
                'started' => false,
                'state' => 'dead',
                'description' => 'Broken service',
            ],
        ], $method->invoke(null, $list));
    }

    public function testParseEmptyList(): void
    {
        $method = new \ReflectionMethod(Systemd::class, 'parseList');

        self::assertSame([], $method->invoke(null, ''));
    }

    public function testUnknownType(): void
    {
        self::assertNull((new Systemd())->run('unknown'));
    }
}
