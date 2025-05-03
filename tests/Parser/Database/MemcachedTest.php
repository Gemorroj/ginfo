<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Memcached;
use PHPUnit\Framework\TestCase;

final class MemcachedTest extends TestCase
{
    public function testMemcached(): void
    {
        $mc = new \Memcached();
        $mc->addServer('127.0.0.1', 11211);
        $mc->addServer('localhost', 11211);
        $mc->add('key1', 'val1', 60);

        $data = (new Memcached())->run($mc);

        self::assertNotEmpty($data['stats']['127.0.0.1:11211']);
        self::assertNotEmpty($data['stats']['localhost:11211']);
        self::assertNotEmpty($data['stats_settings']['127.0.0.1:11211']);
        self::assertNotEmpty($data['stats_settings']['localhost:11211']);
        self::assertNotEmpty($data['stats_conns']['127.0.0.1:11211']);
        self::assertNotEmpty($data['stats_conns']['localhost:11211']);

        self::assertNotEmpty($data['stats']['127.0.0.1:11211']['version']);
        self::assertNotEmpty($data['stats_settings']['127.0.0.1:11211']['inter']);
        self::assertNotEmpty($data['stats_conns']['127.0.0.1:11211']['0:addr']);
    }
}
