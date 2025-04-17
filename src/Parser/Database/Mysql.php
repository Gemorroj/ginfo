<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Mysql implements ParserInterface
{
    /**
     * @return array{
     *     global_status: array<string, string>,
     *     variables: array<string, string>,
     *     performance: array{schema_name: string, count: float|int, avg_microsec: float}[],
     *     summary: array{
     *         host: string,
     *         statement: string,
     *         total: float|int,
     *         total_latency: string,
     *         max_latency: string,
     *         lock_latency: string,
     *         cpu_latency: string,
     *         rows_sent: float|int,
     *         rows_examined: float|int,
     *         rows_affected: float|int,
     *         full_scans: float|int,
     *     }[],
     * }|null
     */
    public function run(\PDO $connection = new \PDO('mysql:host=127.0.0.1', 'root', ''), bool $summary = true): ?array
    {
        $result = [
            'global_status' => [],
            'variables' => [],
            'performance' => [],
            'summary' => [],
        ];
        $query = $connection->query('SHOW GLOBAL STATUS');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['global_status'][$row['Variable_name']] = $row['Value'];
            }
        }
        $query = $connection->query('SHOW VARIABLES');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['variables'][$row['Variable_name']] = $row['Value'];
            }
        }

        $query = $connection->query('
            SELECT schema_name, SUM(count_star) cnt, ROUND((SUM(sum_timer_wait) / SUM(count_star)) / 1000000) AS avg_microsec
            FROM performance_schema.events_statements_summary_by_digest
            WHERE schema_name IS NOT NULL
            GROUP BY schema_name
        ');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['performance'][] = [
                    'schema_name' => $row['schema_name'],
                    'count' => $row['cnt'],
                    'avg_microsec' => $row['avg_microsec'],
                ];
            }
        }

        if ($summary) {
            $query = $connection->query('SELECT * FROM sys.host_summary_by_statement_type');
            if ($query) {
                $result['summary'] = $query->fetchAll(\PDO::FETCH_ASSOC);
            }
        }

        return $result;
    }
}
