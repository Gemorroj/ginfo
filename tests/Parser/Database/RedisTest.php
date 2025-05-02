<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Redis;
use PHPUnit\Framework\TestCase;

final class RedisTest extends TestCase
{
    public function testRedis(): void
    {
        // my local config
        $data = (new Redis())->run(new \Redis(['host' => '127.0.0.1', 'port' => 6379]));

        self::assertNotEmpty($data['server']);
        self::assertNotEmpty($data['clients']);
        self::assertNotEmpty($data['memory']);
        self::assertNotEmpty($data['persistence']);
        self::assertNotEmpty($data['stats']);
        self::assertNotEmpty($data['replication']);
        self::assertNotEmpty($data['cpu']);
        self::assertArrayHasKey('modules', $data);
        self::assertArrayHasKey('errorstats', $data);
        self::assertNotEmpty($data['cluster']);
        self::assertArrayHasKey('keyspace', $data);
        self::assertArrayHasKey('keysizes', $data);
    }
}
