<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Clickhouse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ClickhouseMockTest extends TestCase
{
    public function testRun(): void
    {
        $responses = [
            'SELECT version()' => [
                ['version' => '25.3.1.1', 'uptime' => '3600'],
            ],
            'SELECT metric, value FROM system.metrics' => [
                ['metric' => 'Query', 'value' => 12],
                ['metric' => 'Merge', 'value' => '3'],
            ],
            'SELECT event, value FROM system.events' => [
                ['event' => 'Query', 'value' => '42'],
            ],
            'SELECT metric, value FROM system.asynchronous_metrics' => [
                ['metric' => 'LoadAverage1', 'value' => 1.5],
            ],
            'SELECT query_id, user, elapsed' => [
                ['query_id' => 'abc', 'user' => 'default', 'elapsed' => 0.25, 'read_rows' => '10', 'read_bytes' => '4096', 'memory_usage' => '2048', 'query' => 'SELECT 1'],
            ],
            'SELECT database, table, engine' => [
                ['database' => 'system', 'table' => 'query_log', 'engine' => 'ReplicatedMergeTree', 'is_readonly' => 0, 'absolute_delay' => '2', 'queue_size' => 1, 'total_replicas' => 2, 'active_replicas' => 2],
            ],
            'SELECT name, path, free_space' => [
                ['name' => 'default', 'path' => '/var/lib/clickhouse/', 'free_space' => '1024', 'total_space' => '4096', 'keep_free_space' => '512'],
            ],
        ];

        $httpClient = new MockHttpClient(static function (string $method, string $url, array $options) use ($responses): MockResponse {
            $params = $options['query'] ?? [];
            $query = \is_array($params) ? (string) ($params['query'] ?? '') : '';

            $data = [];
            foreach ($responses as $prefix => $rows) {
                if (\str_starts_with($query, $prefix)) {
                    $data = $rows;
                    break;
                }
            }

            return new MockResponse(\json_encode(['meta' => [], 'data' => $data, 'rows' => \count($data)], \JSON_THROW_ON_ERROR));
        });

        $data = (new Clickhouse())->run('http://clickhouse:8123', 'user', 'pass', $httpClient);

        self::assertNotNull($data);
        self::assertSame('25.3.1.1', $data['version']);
        self::assertSame(3600, $data['uptime']);
        self::assertSame(['Query' => 12, 'Merge' => 3], $data['metrics']);
        self::assertSame(['Query' => 42], $data['events']);
        self::assertSame(['LoadAverage1' => 1.5], $data['asynchronous_metrics']);
        self::assertSame([
            [
                'query_id' => 'abc',
                'user' => 'default',
                'elapsed' => 0.25,
                'read_rows' => 10,
                'read_bytes' => 4096,
                'memory_usage' => 2048,
                'query' => 'SELECT 1',
            ],
        ], $data['processes']);
        self::assertSame([
            [
                'database' => 'system',
                'table' => 'query_log',
                'engine' => 'ReplicatedMergeTree',
                'is_readonly' => false,
                'absolute_delay' => 2,
                'queue_size' => 1,
                'total_replicas' => 2,
                'active_replicas' => 2,
            ],
        ], $data['replicas']);
        self::assertSame([
            [
                'name' => 'default',
                'path' => '/var/lib/clickhouse/',
                'free_space' => 1024,
                'total_space' => 4096,
                'keep_free_space' => 512,
            ],
        ], $data['disks']);
    }

    public function testUnavailable(): void
    {
        $httpClient = new MockHttpClient(static function (): MockResponse {
            return new MockResponse('error', ['http_code' => 500]);
        });

        self::assertNull((new Clickhouse())->run('http://clickhouse:8123', httpClient: $httpClient));
    }
}
