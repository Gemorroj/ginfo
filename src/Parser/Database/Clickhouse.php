<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Clickhouse implements ParserInterface
{
    /**
     * @param string $url HTTP endpoint of the ClickHouse server, http://127.0.0.1:8123 for example
     *
     * @return array{
     *     version: string,
     *     uptime: int,
     *     metrics: array<string, int>,
     *     events: array<string, int>,
     *     asynchronous_metrics: array<string, float>,
     *     processes: array<int, array{
     *         query_id: string,
     *         user: string,
     *         elapsed: float,
     *         read_rows: int,
     *         read_bytes: int,
     *         memory_usage: int,
     *         query: string,
     *     }>,
     *     replicas: array<int, array{
     *         database: string,
     *         table: string,
     *         engine: string,
     *         is_readonly: bool,
     *         absolute_delay: int,
     *         queue_size: int,
     *         total_replicas: int,
     *         active_replicas: int,
     *     }>,
     *     disks: array<int, array{
     *         name: string,
     *         path: string,
     *         free_space: int,
     *         total_space: int,
     *         keep_free_space: int,
     *     }>,
     * }|null
     */
    public function run(
        string $url = 'http://127.0.0.1:8123',
        ?string $username = null,
        ?string $password = null,
        ?HttpClientInterface $httpClient = null,
        int $timeout = 1,
    ): ?array {
        if (null === $httpClient) {
            $options = ['timeout' => (float) $timeout];
            if (null !== $username || null !== $password) {
                $options['auth_basic'] = [$username, $password];
            }
            $httpClient = HttpClient::create($options);
        }

        try {
            $server = self::query($httpClient, $url, 'SELECT version() AS version, uptime() AS uptime');
            $metrics = self::query($httpClient, $url, 'SELECT metric, value FROM system.metrics');
            $events = self::query($httpClient, $url, 'SELECT event, value FROM system.events');
            $asynchronousMetrics = self::query($httpClient, $url, 'SELECT metric, value FROM system.asynchronous_metrics');
            $processes = self::query($httpClient, $url, 'SELECT query_id, user, elapsed, read_rows, read_bytes, memory_usage, query FROM system.processes');
            $replicas = self::query($httpClient, $url, 'SELECT database, table, engine, is_readonly, absolute_delay, queue_size, total_replicas, active_replicas FROM system.replicas');
            $disks = self::query($httpClient, $url, 'SELECT name, path, free_space, total_space, keep_free_space FROM system.disks');
        } catch (\Exception $e) {
            return null;
        }

        return [
            'version' => (string) ($server[0]['version'] ?? ''),
            'uptime' => (int) ($server[0]['uptime'] ?? 0),
            'metrics' => self::toIntMap($metrics, 'metric'),
            'events' => self::toIntMap($events, 'event'),
            'asynchronous_metrics' => self::toFloatMap($asynchronousMetrics, 'metric'),
            'processes' => self::toProcesses($processes),
            'replicas' => self::toReplicas($replicas),
            'disks' => self::toDisks($disks),
        ];
    }

    /**
     * @return array<int, array<mixed>>
     */
    private static function query(HttpClientInterface $httpClient, string $url, string $sql): array
    {
        $json = $httpClient
            ->request('GET', $url, ['query' => ['query' => $sql.' FORMAT JSON']])
            ->toArray();

        $data = $json['data'] ?? [];
        if (!\is_array($data)) {
            return [];
        }

        $rows = [];
        foreach ($data as $row) {
            if (\is_array($row)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @param array<int, array<mixed>> $rows
     *
     * @return array<string, int>
     */
    private static function toIntMap(array $rows, string $key): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[(string) ($row[$key] ?? '')] = (int) ($row['value'] ?? 0);
        }

        return $result;
    }

    /**
     * @param array<int, array<mixed>> $rows
     *
     * @return array<string, float>
     */
    private static function toFloatMap(array $rows, string $key): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[(string) ($row[$key] ?? '')] = (float) ($row['value'] ?? 0);
        }

        return $result;
    }

    /**
     * @param array<int, array<mixed>> $rows
     *
     * @return array<int, array{query_id: string, user: string, elapsed: float, read_rows: int, read_bytes: int, memory_usage: int, query: string}>
     */
    private static function toProcesses(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'query_id' => (string) ($row['query_id'] ?? ''),
                'user' => (string) ($row['user'] ?? ''),
                'elapsed' => (float) ($row['elapsed'] ?? 0),
                'read_rows' => (int) ($row['read_rows'] ?? 0),
                'read_bytes' => (int) ($row['read_bytes'] ?? 0),
                'memory_usage' => (int) ($row['memory_usage'] ?? 0),
                'query' => (string) ($row['query'] ?? ''),
            ];
        }

        return $result;
    }

    /**
     * @param array<int, array<mixed>> $rows
     *
     * @return array<int, array{database: string, table: string, engine: string, is_readonly: bool, absolute_delay: int, queue_size: int, total_replicas: int, active_replicas: int}>
     */
    private static function toReplicas(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'database' => (string) ($row['database'] ?? ''),
                'table' => (string) ($row['table'] ?? ''),
                'engine' => (string) ($row['engine'] ?? ''),
                'is_readonly' => 0 !== (int) ($row['is_readonly'] ?? 0),
                'absolute_delay' => (int) ($row['absolute_delay'] ?? 0),
                'queue_size' => (int) ($row['queue_size'] ?? 0),
                'total_replicas' => (int) ($row['total_replicas'] ?? 0),
                'active_replicas' => (int) ($row['active_replicas'] ?? 0),
            ];
        }

        return $result;
    }

    /**
     * @param array<int, array<mixed>> $rows
     *
     * @return array<int, array{name: string, path: string, free_space: int, total_space: int, keep_free_space: int}>
     */
    private static function toDisks(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'name' => (string) ($row['name'] ?? ''),
                'path' => (string) ($row['path'] ?? ''),
                'free_space' => (int) ($row['free_space'] ?? 0),
                'total_space' => (int) ($row['total_space'] ?? 0),
                'keep_free_space' => (int) ($row['keep_free_space'] ?? 0),
            ];
        }

        return $result;
    }
}
