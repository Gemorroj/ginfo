<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Postgres implements ParserInterface
{
    use DatabaseValueTrait;

    /**
     * @return array{
     *     version: string,
     *     pg_stat_activity: array{
     *         datid: int|null,
     *         datname: string|null,
     *         pid: int,
     *         leader_pid: int|null,
     *         usesysid: int|null,
     *         usename: string|null,
     *         application_name: string|null,
     *         client_addr: string|null,
     *         client_hostname: string|null,
     *         client_port: int|null,
     *         backend_start: \DateTimeImmutable,
     *         xact_start: \DateTimeImmutable|null,
     *         query_start: \DateTimeImmutable|null,
     *         state_change: \DateTimeImmutable|null,
     *         wait_event_type: string|null,
     *         wait_event: string|null,
     *         state: string|null,
     *         backend_xid: int|null,
     *         backend_xmin: int|null,
     *         query_id: int|float|null,
     *         query: string|null,
     *         backend_type: string,
     *     }[],
     *     pg_stat_database: array{
     *         datid: int,
     *         datname: string|null,
     *         numbackends: int,
     *         xact_commit: int|float,
     *         xact_rollback: int|float,
     *         blks_read: int|float,
     *         blks_hit: int|float,
     *         tup_returned: int|float,
     *         tup_fetched: int|float,
     *         tup_inserted: int|float,
     *         tup_updated: int|float,
     *         tup_deleted: int|float,
     *         conflicts: int|float,
     *         temp_files: int|float,
     *         temp_bytes: int|float,
     *         deadlocks: int|float,
     *         checksum_failures: int|float|null,
     *         checksum_last_failure: \DateTimeImmutable|null,
     *         blk_read_time: float,
     *         blk_write_time: float,
     *         session_time: float,
     *         active_time: float,
     *         idle_in_transaction_time: float,
     *         sessions: int|float,
     *         sessions_abandoned: int|float,
     *         sessions_fatal: int|float,
     *         sessions_killed: int|float,
     *         stats_reset: \DateTimeImmutable|null,
     *     }[],
     *     pg_stat_all_tables: array{
     *         relid: int,
     *         schemaname: string,
     *         relname: string,
     *         seq_scan: int|float,
     *         last_seq_scan: \DateTimeImmutable|null,
     *         seq_tup_read: int|float,
     *         idx_scan: int|float,
     *         last_idx_scan: \DateTimeImmutable|null,
     *         idx_tup_fetch: int|float,
     *         n_tup_ins: int|float,
     *         n_tup_upd: int|float,
     *         n_tup_del: int|float,
     *         n_tup_hot_upd: int|float,
     *         n_tup_newpage_upd: int|float,
     *         n_live_tup: int|float,
     *         n_dead_tup: int|float,
     *         n_mod_since_analyze: int|float,
     *         n_ins_since_vacuum: int|float,
     *         last_vacuum: \DateTimeImmutable|null,
     *         last_autovacuum: \DateTimeImmutable|null,
     *         last_analyze: \DateTimeImmutable|null,
     *         last_autoanalyze: \DateTimeImmutable|null,
     *         vacuum_count: int|float,
     *         autovacuum_count: int|float,
     *         analyze_count: int|float,
     *         autoanalyze_count: int|float,
     *     }[],
     *     pg_stat_all_indexes: array{
     *         relid: int,
     *         indexrelid: int,
     *         schemaname: string,
     *         relname: string,
     *         indexrelname: string,
     *         idx_scan: int|float,
     *         last_idx_scan: \DateTimeImmutable|null,
     *         idx_tup_read: int|float,
     *         idx_tup_fetch: int|float,
     *     }[],
     *     pg_stat_statements: array{
     *         userid: int,
     *         dbid: int,
     *         toplevel: bool,
     *         queryid: int|float|null,
     *         query: string,
     *         plans: int|float,
     *         total_plan_time: float,
     *         min_plan_time: float,
     *         max_plan_time: float,
     *         mean_plan_time: float,
     *         stddev_plan_time: float,
     *         calls: int|float,
     *         total_exec_time: float,
     *         min_exec_time: float,
     *         max_exec_time: float,
     *         mean_exec_time: float,
     *         stddev_exec_time: float,
     *         rows: int|float,
     *         shared_blks_hit: int|float,
     *         shared_blks_read: int|float,
     *         shared_blks_dirtied: int|float,
     *         shared_blks_written: int|float,
     *         local_blks_hit: int|float,
     *         local_blks_read: int|float,
     *         local_blks_dirtied: int|float,
     *         local_blks_written: int|float,
     *         temp_blks_read: int|float,
     *         temp_blks_written: int|float,
     *         shared_blk_read_time: float,
     *         shared_blk_write_time: float,
     *         local_blk_read_time: float,
     *         local_blk_write_time: float,
     *         temp_blk_read_time: float,
     *         temp_blk_write_time: float,
     *         wal_records: int|float,
     *         wal_fpi: int|float,
     *         wal_bytes: float,
     *         jit_functions: int|float,
     *         jit_generation_time: float,
     *         jit_inlining_count: int|float,
     *         jit_inlining_time: float,
     *         jit_optimization_count: int|float,
     *         jit_optimization_time: float,
     *         jit_emission_count: int|float,
     *         jit_emission_time: float,
     *         jit_deform_count: int|float,
     *         jit_deform_time: float,
     *         stats_since: \DateTimeImmutable|null,
     *         minmax_stats_since: \DateTimeImmutable|null,
     *      }[],
     * }
     */
    public function run(\PDO $connection = new \PDO('pgsql:host=127.0.0.1', 'postgres', 'postgres')): array
    {
        $result = [
            'version' => null,
            'pg_stat_activity' => [],
            'pg_stat_database' => [],
            'pg_stat_all_tables' => [],
            'pg_stat_all_indexes' => [],
            'pg_stat_statements' => [],
        ];

        $query = $connection->query('SELECT version()');
        if ($query) {
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            if (false !== $row) {
                $result['version'] = $row['version'];
            }
        }

        try {
            $query = $connection->query('SELECT * FROM pg_stat_activity');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['pg_stat_activity'][] = [
                        'datid' => $row['datid'],
                        'datname' => $row['datname'],
                        'pid' => $row['pid'],
                        'leader_pid' => $row['leader_pid'],
                        'usesysid' => $row['usesysid'],
                        'usename' => $row['usename'],
                        'application_name' => $row['application_name'],
                        'client_addr' => $row['client_addr'],
                        'client_hostname' => $row['client_hostname'],
                        'client_port' => $row['client_port'],
                        'backend_start' => new \DateTimeImmutable($row['backend_start']),
                        'xact_start' => self::toDateOrNull($row['xact_start'] ?? null),
                        'query_start' => self::toDateOrNull($row['query_start'] ?? null),
                        'state_change' => self::toDateOrNull($row['state_change'] ?? null),
                        'wait_event_type' => $row['wait_event_type'],
                        'wait_event' => $row['wait_event'],
                        'state' => $row['state'],
                        'backend_xid' => $row['backend_xid'],
                        'backend_xmin' => $row['backend_xmin'],
                        'query_id' => self::toNumberOrNull($row['query_id'] ?? null),
                        'query' => $row['query'],
                        'backend_type' => $row['backend_type'],
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }
        try {
            $query = $connection->query('SELECT * FROM pg_stat_database');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['pg_stat_database'][] = [
                        'datid' => $row['datid'],
                        'datname' => $row['datname'],
                        'numbackends' => $row['numbackends'],
                        'xact_commit' => self::toNumberOrZero($row['xact_commit'] ?? null),
                        'xact_rollback' => self::toNumberOrZero($row['xact_rollback'] ?? null),
                        'blks_read' => self::toNumberOrZero($row['blks_read'] ?? null),
                        'blks_hit' => self::toNumberOrZero($row['blks_hit'] ?? null),
                        'tup_returned' => self::toNumberOrZero($row['tup_returned'] ?? null),
                        'tup_fetched' => self::toNumberOrZero($row['tup_fetched'] ?? null),
                        'tup_inserted' => self::toNumberOrZero($row['tup_inserted'] ?? null),
                        'tup_updated' => self::toNumberOrZero($row['tup_updated'] ?? null),
                        'tup_deleted' => self::toNumberOrZero($row['tup_deleted'] ?? null),
                        'conflicts' => self::toNumberOrZero($row['conflicts'] ?? null),
                        'temp_files' => self::toNumberOrZero($row['temp_files'] ?? null),
                        'temp_bytes' => self::toNumberOrZero($row['temp_bytes'] ?? null),
                        'deadlocks' => self::toNumberOrZero($row['deadlocks'] ?? null),
                        'checksum_failures' => self::toNumberOrNull($row['checksum_failures'] ?? null),
                        'checksum_last_failure' => self::toDateOrNull($row['checksum_last_failure'] ?? null),
                        'blk_read_time' => (float) ($row['blk_read_time'] ?? 0),
                        'blk_write_time' => (float) ($row['blk_write_time'] ?? 0),
                        'session_time' => (float) ($row['session_time'] ?? 0),
                        'active_time' => (float) ($row['active_time'] ?? 0),
                        'idle_in_transaction_time' => (float) ($row['idle_in_transaction_time'] ?? 0),
                        'sessions' => self::toNumberOrZero($row['sessions'] ?? null),
                        'sessions_abandoned' => self::toNumberOrZero($row['sessions_abandoned'] ?? null),
                        'sessions_fatal' => self::toNumberOrZero($row['sessions_fatal'] ?? null),
                        'sessions_killed' => self::toNumberOrZero($row['sessions_killed'] ?? null),
                        'stats_reset' => self::toDateOrNull($row['stats_reset'] ?? null),
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }
        try {
            $query = $connection->query('SELECT * FROM pg_stat_all_tables');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['pg_stat_all_tables'][] = [
                        'relid' => $row['relid'],
                        'schemaname' => $row['schemaname'],
                        'relname' => $row['relname'],
                        'seq_scan' => self::toNumberOrZero($row['seq_scan'] ?? null),
                        'last_seq_scan' => self::toDateOrNull($row['last_seq_scan'] ?? null),
                        'seq_tup_read' => self::toNumberOrZero($row['seq_tup_read'] ?? null),
                        'idx_scan' => self::toNumberOrZero($row['idx_scan'] ?? null),
                        'last_idx_scan' => self::toDateOrNull($row['last_idx_scan'] ?? null),
                        'idx_tup_fetch' => self::toNumberOrZero($row['idx_tup_fetch'] ?? null),
                        'n_tup_ins' => self::toNumberOrZero($row['n_tup_ins'] ?? null),
                        'n_tup_upd' => self::toNumberOrZero($row['n_tup_upd'] ?? null),
                        'n_tup_del' => self::toNumberOrZero($row['n_tup_del'] ?? null),
                        'n_tup_hot_upd' => self::toNumberOrZero($row['n_tup_hot_upd'] ?? null),
                        'n_tup_newpage_upd' => self::toNumberOrZero($row['n_tup_newpage_upd'] ?? null),
                        'n_live_tup' => self::toNumberOrZero($row['n_live_tup'] ?? null),
                        'n_dead_tup' => self::toNumberOrZero($row['n_dead_tup'] ?? null),
                        'n_mod_since_analyze' => self::toNumberOrZero($row['n_mod_since_analyze'] ?? null),
                        'n_ins_since_vacuum' => self::toNumberOrZero($row['n_ins_since_vacuum'] ?? null),
                        'last_vacuum' => self::toDateOrNull($row['last_vacuum'] ?? null),
                        'last_autovacuum' => self::toDateOrNull($row['last_autovacuum'] ?? null),
                        'last_analyze' => self::toDateOrNull($row['last_analyze'] ?? null),
                        'last_autoanalyze' => self::toDateOrNull($row['last_autoanalyze'] ?? null),
                        'vacuum_count' => self::toNumberOrZero($row['vacuum_count'] ?? null),
                        'autovacuum_count' => self::toNumberOrZero($row['autovacuum_count'] ?? null),
                        'analyze_count' => self::toNumberOrZero($row['analyze_count'] ?? null),
                        'autoanalyze_count' => self::toNumberOrZero($row['autoanalyze_count'] ?? null),
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }
        try {
            $query = $connection->query('SELECT * FROM pg_stat_all_indexes');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['pg_stat_all_indexes'][] = [
                        'relid' => $row['relid'],
                        'indexrelid' => $row['indexrelid'],
                        'schemaname' => $row['schemaname'],
                        'relname' => $row['relname'],
                        'indexrelname' => $row['indexrelname'],
                        'idx_scan' => self::toNumberOrZero($row['idx_scan'] ?? null),
                        'last_idx_scan' => self::toDateOrNull($row['last_idx_scan'] ?? null),
                        'idx_tup_read' => self::toNumberOrZero($row['idx_tup_read'] ?? null),
                        'idx_tup_fetch' => self::toNumberOrZero($row['idx_tup_fetch'] ?? null),
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }
        try {
            $query = $connection->query('SELECT * FROM pg_stat_statements');
            if ($query) {
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $result['pg_stat_statements'][] = [
                        'userid' => $row['userid'],
                        'dbid' => $row['dbid'],
                        'toplevel' => (bool) ($row['toplevel'] ?? true),
                        'queryid' => self::toNumberOrNull($row['queryid'] ?? null),
                        'query' => $row['query'],
                        'plans' => self::toNumberOrZero($row['plans'] ?? null),
                        'total_plan_time' => (float) ($row['total_plan_time'] ?? 0),
                        'min_plan_time' => (float) ($row['min_plan_time'] ?? 0),
                        'max_plan_time' => (float) ($row['max_plan_time'] ?? 0),
                        'mean_plan_time' => (float) ($row['mean_plan_time'] ?? 0),
                        'stddev_plan_time' => (float) ($row['stddev_plan_time'] ?? 0),
                        'calls' => self::toNumberOrZero($row['calls'] ?? null),
                        'total_exec_time' => (float) ($row['total_exec_time'] ?? 0),
                        'min_exec_time' => (float) ($row['min_exec_time'] ?? 0),
                        'max_exec_time' => (float) ($row['max_exec_time'] ?? 0),
                        'mean_exec_time' => (float) ($row['mean_exec_time'] ?? 0),
                        'stddev_exec_time' => (float) ($row['stddev_exec_time'] ?? 0),
                        'rows' => self::toNumberOrZero($row['rows'] ?? null),
                        'shared_blks_hit' => self::toNumberOrZero($row['shared_blks_hit'] ?? null),
                        'shared_blks_read' => self::toNumberOrZero($row['shared_blks_read'] ?? null),
                        'shared_blks_dirtied' => self::toNumberOrZero($row['shared_blks_dirtied'] ?? null),
                        'shared_blks_written' => self::toNumberOrZero($row['shared_blks_written'] ?? null),
                        'local_blks_hit' => self::toNumberOrZero($row['local_blks_hit'] ?? null),
                        'local_blks_read' => self::toNumberOrZero($row['local_blks_read'] ?? null),
                        'local_blks_dirtied' => self::toNumberOrZero($row['local_blks_dirtied'] ?? null),
                        'local_blks_written' => self::toNumberOrZero($row['local_blks_written'] ?? null),
                        'temp_blks_read' => self::toNumberOrZero($row['temp_blks_read'] ?? null),
                        'temp_blks_written' => self::toNumberOrZero($row['temp_blks_written'] ?? null),
                        'shared_blk_read_time' => (float) ($row['shared_blk_read_time'] ?? $row['blk_read_time'] ?? 0),
                        'shared_blk_write_time' => (float) ($row['shared_blk_write_time'] ?? $row['blk_write_time'] ?? 0),
                        'local_blk_read_time' => (float) ($row['local_blk_read_time'] ?? 0),
                        'local_blk_write_time' => (float) ($row['local_blk_write_time'] ?? 0),
                        'temp_blk_read_time' => (float) ($row['temp_blk_read_time'] ?? 0),
                        'temp_blk_write_time' => (float) ($row['temp_blk_write_time'] ?? 0),
                        'wal_records' => self::toNumberOrZero($row['wal_records'] ?? null),
                        'wal_fpi' => self::toNumberOrZero($row['wal_fpi'] ?? null),
                        'wal_bytes' => (float) ($row['wal_bytes'] ?? 0),
                        'jit_functions' => self::toNumberOrZero($row['jit_functions'] ?? null),
                        'jit_generation_time' => (float) ($row['jit_generation_time'] ?? 0),
                        'jit_inlining_count' => self::toNumberOrZero($row['jit_inlining_count'] ?? null),
                        'jit_inlining_time' => (float) ($row['jit_inlining_time'] ?? 0),
                        'jit_optimization_count' => self::toNumberOrZero($row['jit_optimization_count'] ?? null),
                        'jit_optimization_time' => (float) ($row['jit_optimization_time'] ?? 0),
                        'jit_emission_count' => self::toNumberOrZero($row['jit_emission_count'] ?? null),
                        'jit_emission_time' => (float) ($row['jit_emission_time'] ?? 0),
                        'jit_deform_count' => self::toNumberOrZero($row['jit_deform_count'] ?? null),
                        'jit_deform_time' => (float) ($row['jit_deform_time'] ?? 0),
                        'stats_since' => self::toDateOrNull($row['stats_since'] ?? null),
                        'minmax_stats_since' => self::toDateOrNull($row['minmax_stats_since'] ?? null),
                    ];
                }
            }
        } catch (\Exception) {
            // ignore
        }

        return $result;
    }
}
