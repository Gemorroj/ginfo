<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Mysql implements ParserInterface
{
    /**
     * @return array{
     *     global_status: array<string, string>,
     *     global_variables: array<string, string>,
     *     performance_95th_percentile: array{
     *         query: string,
     *         db: string,
     *         full_scan: bool,
     *         exec_count: float|int,
     *         err_count: float|int,
     *         warn_count: float|int,
     *         total_latency: array{value: float, unit: string},
     *         max_latency: array{value: float, unit: string},
     *         avg_latency: array{value: float, unit: string},
     *         rows_sent: float|int,
     *         rows_sent_avg: float|int,
     *         rows_examined: float|int,
     *         rows_examined_avg: float|int,
     *         first_seen: \DateTimeImmutable,
     *         last_seen: \DateTimeImmutable,
     *         digest: string,
     *     }[],
     *     count_queries: array{
     *         object_type: string,
     *         object_schema: string,
     *         object_name: string,
     *         count_read: float|int,
     *         count_write: float|int,
     *         count_fetch: float|int,
     *         count_insert: float|int,
     *         count_update: float|int,
     *         count_delete: float|int,
     *     }[],
     *     data_length: array{
     *         table_schema: string,
     *         table_name: string,
     *         data_length: float|int,
     *         index_length: float|int,
     *     }[]
     * }|null
     */
    public function run(\PDO $connection = new \PDO('mysql:host=127.0.0.1', 'root', '')): ?array
    {
        $result = [
            'global_status' => [],
            'global_variables' => [],
            'performance_95th_percentile' => [],
            'count_queries' => [],
            'data_length' => [],
        ];

        $query = $connection->query('SHOW GLOBAL STATUS');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['global_status'][$row['Variable_name']] = $row['Value'];
            }
        }

        $query = $connection->query('SHOW GLOBAL VARIABLES');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['global_variables'][$row['Variable_name']] = $row['Value'];
            }
        }

        try {
            $query = $connection->query('SELECT * FROM sys.statements_with_runtimes_in_95th_percentile');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $totalLatency = \explode(' ', $row['total_latency']);
                    $maxLatency = \explode(' ', $row['max_latency']);
                    $avgLatency = \explode(' ', $row['avg_latency']);

                    $result['performance_95th_percentile'][] = [
                        'query' => $row['query'],
                        'db' => $row['db'],
                        'full_scan' => (bool) $row['full_scan'],
                        'exec_count' => self::convertToNumber($row['exec_count']),
                        'err_count' => self::convertToNumber($row['err_count']),
                        'warn_count' => self::convertToNumber($row['warn_count']),
                        'total_latency' => [
                            'value' => (float) $totalLatency[0],
                            'unit' => \trim($totalLatency[1]),
                        ],
                        'max_latency' => [
                            'value' => (float) $maxLatency[0],
                            'unit' => \trim($maxLatency[1]),
                        ],
                        'avg_latency' => [
                            'value' => (float) $avgLatency[0],
                            'unit' => \trim($avgLatency[1]),
                        ],
                        'rows_sent' => self::convertToNumber($row['rows_sent']),
                        'rows_sent_avg' => self::convertToNumber($row['rows_sent_avg']),
                        'rows_examined' => self::convertToNumber($row['rows_examined']),
                        'rows_examined_avg' => self::convertToNumber($row['rows_examined_avg']),
                        'first_seen' => new \DateTimeImmutable($row['first_seen']),
                        'last_seen' => new \DateTimeImmutable($row['last_seen']),
                        'digest' => $row['digest'],
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }

        try {
            $query = $connection->query('
                SELECT object_type, object_schema, object_name, count_read, count_write, count_fetch, count_insert, count_update, count_delete
                FROM performance_schema.table_io_waits_summary_by_table
                WHERE count_star > 0
                ORDER BY count_star DESC
            ');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['count_queries'][] = [
                        'object_type' => $row['object_type'],
                        'object_schema' => $row['object_schema'],
                        'object_name' => $row['object_name'],
                        'count_read' => self::convertToNumber($row['count_read']),
                        'count_write' => self::convertToNumber($row['count_write']),
                        'count_fetch' => self::convertToNumber($row['count_fetch']),
                        'count_insert' => self::convertToNumber($row['count_insert']),
                        'count_update' => self::convertToNumber($row['count_update']),
                        'count_delete' => self::convertToNumber($row['count_delete']),
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }

        try {
            $query = $connection->query('
                SELECT table_schema AS `table_schema`, table_name AS `table_name`, SUM(data_length) AS `data_length`, SUM(index_length) AS `index_length`
                FROM information_schema.tables
                GROUP BY table_schema, table_name
                ORDER BY `table_schema` ASC, `table_name` ASC
            ');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['data_length'][] = [
                        'table_schema' => $row['table_schema'],
                        'table_name' => $row['table_name'],
                        'data_length' => self::convertToNumber($row['data_length']),
                        'index_length' => self::convertToNumber($row['index_length']),
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }

        return $result;
    }

    private static function convertToNumber(?string $number): int|float|null
    {
        if (null === $number) {
            return null;
        }
        if ($number > \PHP_INT_MAX) {
            return (float) $number;
        }
        if ((string) (int) $number !== $number) {
            return (float) $number;
        }

        return (int) $number;
    }
}
