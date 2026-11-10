<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Ginfo;
use Ginfo\Parser\Database\Clickhouse;
use PHPUnit\Framework\TestCase;

final class ClickhouseTest extends TestCase
{
    public function testClickhouse(): void
    {
        $data = (new Clickhouse())->run('http://127.0.0.1:8123', 'default', 'clickhouse');
        if (null === $data) {
            self::markTestSkipped('ClickHouse is not found');
        }

        self::assertNotEmpty($data['version']);
        self::assertGreaterThan(0, $data['uptime']);
        self::assertArrayHasKey('Query', $data['metrics']);
        self::assertNotEmpty($data['asynchronous_metrics']);
        self::assertNotEmpty($data['disks']);

        $clickhouse = (new Ginfo())->getClickhouse('http://127.0.0.1:8123', 'default', 'clickhouse');
        self::assertNotNull($clickhouse);
        self::assertSame($data['version'], $clickhouse->getVersion());
        self::assertSame($data['uptime'], $clickhouse->getUptime());
        self::assertNotEmpty($clickhouse->getMetrics());
        self::assertNotEmpty($clickhouse->getDisks());
    }
}
