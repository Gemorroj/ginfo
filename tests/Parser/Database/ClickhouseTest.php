<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Ginfo;
use Ginfo\Parser\Database\Clickhouse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\NativeHttpClient;

final class ClickhouseTest extends TestCase
{
    public function testClickhouse(): void
    {
        // The curl-based client segfaults on PHP 8.2 in the GitHub Actions runner.
        // Auth is configured on the client because Clickhouse only applies credentials to its own client.
        $httpClient = new NativeHttpClient([
            'timeout' => 1,
            'auth_basic' => ['default', 'clickhouse'],
        ]);
        $data = (new Clickhouse())->run('http://127.0.0.1:8123', 'default', 'clickhouse', $httpClient);
        if (null === $data) {
            self::markTestSkipped('ClickHouse is not found');
        }

        self::assertNotEmpty($data['version']);
        self::assertGreaterThan(0, $data['uptime']);
        self::assertArrayHasKey('Query', $data['metrics']);
        self::assertNotEmpty($data['asynchronous_metrics']);
        self::assertNotEmpty($data['disks']);

        $clickhouse = (new Ginfo())->getClickhouse('http://127.0.0.1:8123', 'default', 'clickhouse', $httpClient);
        self::assertNotNull($clickhouse);
        self::assertSame($data['version'], $clickhouse->getVersion());
        self::assertGreaterThanOrEqual($data['uptime'], $clickhouse->getUptime());
        self::assertNotEmpty($clickhouse->getMetrics());
        self::assertNotEmpty($clickhouse->getDisks());
    }
}
