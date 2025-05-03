<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Postgres implements ParserInterface
{
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
     *         queryid: int|float,
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
     *         stats_since: \DateTimeImmutable,
     *         minmax_stats_since: \DateTimeImmutable,
     *      }[],
     * }|null
     */
    public function run(\PDO $connection = new \PDO('pgsql:host=127.0.0.1', 'postgres', 'postgres')): ?array
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
            $result['version'] = $row['version'];
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
                        'xact_start' => $row['xact_start'] ? new \DateTimeImmutable($row['xact_start']) : null,
                        'query_start' => $row['query_start'] ? new \DateTimeImmutable($row['query_start']) : null,
                        'state_change' => $row['state_change'] ? new \DateTimeImmutable($row['state_change']) : null,
                        'wait_event_type' => $row['wait_event_type'],
                        'wait_event' => $row['wait_event'],
                        'state' => $row['state'],
                        'backend_xid' => $row['backend_xid'],
                        'backend_xmin' => $row['backend_xmin'],
                        'query_id' => self::convertToNumber($row['query_id']),
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
                        'xact_commit' => self::convertToNumber($row['xact_commit']),
                        'xact_rollback' => self::convertToNumber($row['xact_rollback']),
                        'blks_read' => self::convertToNumber($row['blks_read']),
                        'blks_hit' => self::convertToNumber($row['blks_hit']),
                        'tup_returned' => self::convertToNumber($row['tup_returned']),
                        'tup_fetched' => self::convertToNumber($row['tup_fetched']),
                        'tup_inserted' => self::convertToNumber($row['tup_inserted']),
                        'tup_updated' => self::convertToNumber($row['tup_updated']),
                        'tup_deleted' => self::convertToNumber($row['tup_deleted']),
                        'conflicts' => self::convertToNumber($row['conflicts']),
                        'temp_files' => self::convertToNumber($row['temp_files']),
                        'temp_bytes' => self::convertToNumber($row['temp_bytes']),
                        'deadlocks' => self::convertToNumber($row['deadlocks']),
                        'checksum_failures' => self::convertToNumber($row['checksum_failures']),
                        'checksum_last_failure' => $row['checksum_last_failure'] ? new \DateTimeImmutable($row['checksum_last_failure']) : null,
                        'blk_read_time' => $row['blk_read_time'],
                        'blk_write_time' => $row['blk_write_time'],
                        'session_time' => $row['session_time'],
                        'active_time' => $row['active_time'],
                        'idle_in_transaction_time' => $row['idle_in_transaction_time'],
                        'sessions' => self::convertToNumber($row['sessions']),
                        'sessions_abandoned' => self::convertToNumber($row['sessions_abandoned']),
                        'sessions_fatal' => self::convertToNumber($row['sessions_fatal']),
                        'sessions_killed' => self::convertToNumber($row['sessions_killed']),
                        'stats_reset' => $row['stats_reset'] ? new \DateTimeImmutable($row['stats_reset']) : null,
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
                        'seq_scan' => self::convertToNumber($row['seq_scan']),
                        'last_seq_scan' => $row['stats_reset'] ? new \DateTimeImmutable($row['last_seq_scan']) : null,
                        'seq_tup_read' => self::convertToNumber($row['seq_tup_read']),
                        'idx_scan' => self::convertToNumber($row['idx_scan']),
                        'last_idx_scan' => $row['last_idx_scan'] ? new \DateTimeImmutable($row['last_idx_scan']) : null,
                        'idx_tup_fetch' => self::convertToNumber($row['idx_tup_fetch']),
                        'n_tup_ins' => self::convertToNumber($row['n_tup_ins']),
                        'n_tup_upd' => self::convertToNumber($row['n_tup_upd']),
                        'n_tup_del' => self::convertToNumber($row['n_tup_del']),
                        'n_tup_hot_upd' => self::convertToNumber($row['n_tup_hot_upd']),
                        'n_tup_newpage_upd' => self::convertToNumber($row['n_tup_newpage_upd']),
                        'n_live_tup' => self::convertToNumber($row['n_live_tup']),
                        'n_dead_tup' => self::convertToNumber($row['n_dead_tup']),
                        'n_mod_since_analyze' => self::convertToNumber($row['n_mod_since_analyze']),
                        'n_ins_since_vacuum' => self::convertToNumber($row['n_ins_since_vacuum']),
                        'last_vacuum' => $row['last_vacuum'] ? new \DateTimeImmutable($row['last_vacuum']) : null,
                        'last_autovacuum' => $row['last_autovacuum'] ? new \DateTimeImmutable($row['last_autovacuum']) : null,
                        'last_analyze' => $row['last_analyze'] ? new \DateTimeImmutable($row['last_analyze']) : null,
                        'last_autoanalyze' => $row['last_autoanalyze'] ? new \DateTimeImmutable($row['last_autoanalyze']) : null,
                        'vacuum_count' => self::convertToNumber($row['vacuum_count']),
                        'autovacuum_count' => self::convertToNumber($row['autovacuum_count']),
                        'analyze_count' => self::convertToNumber($row['analyze_count']),
                        'autoanalyze_count' => self::convertToNumber($row['autoanalyze_count']),
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
                        'idx_scan' => self::convertToNumber($row['idx_scan']),
                        'last_idx_scan' => $row['last_idx_scan'] ? new \DateTimeImmutable($row['last_idx_scan']) : null,
                        'idx_tup_read' => self::convertToNumber($row['idx_tup_read']),
                        'idx_tup_fetch' => self::convertToNumber($row['idx_tup_fetch']),
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
                        'toplevel' => $row['toplevel'],
                        'queryid' => self::convertToNumber($row['queryid']),
                        'query' => $row['query'],
                        'plans' => self::convertToNumber($row['plans']),
                        'total_plan_time' => $row['total_plan_time'],
                        'min_plan_time' => $row['min_plan_time'],
                        'max_plan_time' => $row['max_plan_time'],
                        'mean_plan_time' => $row['mean_plan_time'],
                        'stddev_plan_time' => $row['stddev_plan_time'],
                        'calls' => self::convertToNumber($row['calls']),
                        'total_exec_time' => $row['total_exec_time'],
                        'min_exec_time' => $row['min_exec_time'],
                        'max_exec_time' => $row['max_exec_time'],
                        'mean_exec_time' => $row['mean_exec_time'],
                        'stddev_exec_time' => $row['stddev_exec_time'],
                        'rows' => self::convertToNumber($row['rows']),
                        'shared_blks_hit' => self::convertToNumber($row['shared_blks_hit']),
                        'shared_blks_read' => self::convertToNumber($row['shared_blks_read']),
                        'shared_blks_dirtied' => self::convertToNumber($row['shared_blks_dirtied']),
                        'shared_blks_written' => self::convertToNumber($row['shared_blks_written']),
                        'local_blks_hit' => self::convertToNumber($row['local_blks_hit']),
                        'local_blks_read' => self::convertToNumber($row['local_blks_read']),
                        'local_blks_dirtied' => self::convertToNumber($row['local_blks_dirtied']),
                        'local_blks_written' => self::convertToNumber($row['local_blks_written']),
                        'temp_blks_read' => self::convertToNumber($row['temp_blks_read']),
                        'temp_blks_written' => self::convertToNumber($row['temp_blks_written']),
                        'shared_blk_read_time' => $row['shared_blk_read_time'],
                        'shared_blk_write_time' => $row['shared_blk_write_time'],
                        'local_blk_read_time' => $row['local_blk_read_time'],
                        'local_blk_write_time' => $row['local_blk_write_time'],
                        'temp_blk_read_time' => $row['temp_blk_read_time'],
                        'temp_blk_write_time' => $row['temp_blk_write_time'],
                        'wal_records' => self::convertToNumber($row['wal_records']),
                        'wal_fpi' => self::convertToNumber($row['wal_fpi']),
                        'wal_bytes' => $row['wal_bytes'],
                        'jit_functions' => self::convertToNumber($row['jit_functions']),
                        'jit_generation_time' => $row['jit_generation_time'],
                        'jit_inlining_count' => self::convertToNumber($row['jit_inlining_count']),
                        'jit_inlining_time' => $row['jit_inlining_time'],
                        'jit_optimization_count' => self::convertToNumber($row['jit_optimization_count']),
                        'jit_optimization_time' => $row['jit_optimization_time'],
                        'jit_emission_count' => self::convertToNumber($row['jit_emission_count']),
                        'jit_emission_time' => $row['jit_emission_time'],
                        'jit_deform_count' => self::convertToNumber($row['jit_deform_count']),
                        'jit_deform_time' => $row['jit_deform_time'],
                        'stats_since' => new \DateTimeImmutable($row['stats_since']),
                        'minmax_stats_since' => new \DateTimeImmutable($row['minmax_stats_since']),
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
