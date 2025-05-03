<?php

namespace Ginfo;

use Ginfo\Exception\UnknownParserException;
use Ginfo\Info\Battery;
use Ginfo\Info\Cpu;
use Ginfo\Info\Database\Manticore;
use Ginfo\Info\Database\Mysql;
use Ginfo\Info\Database\MysqlCountQueries;
use Ginfo\Info\Database\MysqlDataLength;
use Ginfo\Info\Database\MysqlPerformance95thPercentile;
use Ginfo\Info\Database\Postgres;
use Ginfo\Info\Database\PostgresPgStatActivity;
use Ginfo\Info\Database\PostgresPgStatAllIndexes;
use Ginfo\Info\Database\PostgresPgStatAllTables;
use Ginfo\Info\Database\PostgresPgStatDatabase;
use Ginfo\Info\Database\PostgresPgStatStatements;
use Ginfo\Info\Database\Redis;
use Ginfo\Info\Database\Sqlite;
use Ginfo\Info\Database\SqlitePragma;
use Ginfo\Info\Database\SqlitePragmaTable;
use Ginfo\Info\Disk;
use Ginfo\Info\General;
use Ginfo\Info\InfoInterface;
use Ginfo\Info\Memory;
use Ginfo\Info\Network;
use Ginfo\Info\Pci;
use Ginfo\Info\Php;
use Ginfo\Info\Printer;
use Ginfo\Info\Process;
use Ginfo\Info\Samba;
use Ginfo\Info\Selinux;
use Ginfo\Info\Sensor;
use Ginfo\Info\Service;
use Ginfo\Info\SoundCard;
use Ginfo\Info\Ups;
use Ginfo\Info\Usb;
use Ginfo\Info\WebServer\Angie;
use Ginfo\Info\WebServer\Caddy;
use Ginfo\Info\WebServer\CaddyBuildInfo;
use Ginfo\Info\WebServer\Httpd;
use Ginfo\Info\WebServer\HttpdStatus;
use Ginfo\Info\WebServer\Nginx;
use Ginfo\Os\Linux;
use Ginfo\Os\OsInterface;

final readonly class Ginfo
{
    /**
     * @param InfoParserInterface[] $customParsers
     */
    public function __construct(private OsInterface $os = new Linux(), private array $customParsers = [])
    {
    }

    /**
     * @param class-string<InfoParserInterface> $parserName
     *
     * @throws UnknownParserException
     */
    public function getCustomParser(string $parserName): ?InfoInterface
    {
        foreach ($this->customParsers as $customParser) {
            if ($customParser::class === $parserName) {
                return $customParser->run();
            }
        }

        throw new UnknownParserException('Unknown parser: '.$parserName);
    }

    /**
     * General info.
     */
    public function getGeneral(): General
    {
        $uptimeTimestamp = $this->os->getUptime();
        if ($uptimeTimestamp) {
            $startDate = new \DateTimeImmutable('now - '.$uptimeTimestamp.' seconds');
            $endDate = new \DateTimeImmutable('now');

            $uptime = $startDate->diff($endDate);
        } else {
            $uptime = null;
        }

        return new General(
            new \DateTimeImmutable(),
            $this->os->getOsName(),
            $this->os->getKernel(),
            $this->os->getHostName(),
            $this->os->getArchitecture(),
            $uptime,
            $this->os->getVirtualization(),
            $this->os->getLoggedUsers(),
            $this->os->getModel(),
            $this->os->getLoad(),
        );
    }

    /**
     * CPU info.
     */
    public function getCpu(): ?Cpu
    {
        return $this->os->getCpu();
    }

    /**
     * Memory info.
     */
    public function getMemory(): ?Memory
    {
        return $this->os->getMemory();
    }

    /**
     * USB devices.
     *
     * @return Usb[]
     */
    public function getUsb(): array
    {
        return $this->os->getUsb() ?? [];
    }

    /**
     * PCI devices.
     *
     * @return Pci[]
     */
    public function getPci(): array
    {
        return $this->os->getPci() ?? [];
    }

    /**
     * Sound cards.
     *
     * @return SoundCard[]
     */
    public function getSoundCard(): array
    {
        return $this->os->getSoundCards() ?? [];
    }

    /**
     * Network devices.
     *
     * @return Network[]
     */
    public function getNetwork(): array
    {
        return $this->os->getNetwork() ?? [];
    }

    /**
     * Battery status.
     *
     * @return Battery[]
     */
    public function getBattery(): array
    {
        return $this->os->getBattery() ?? [];
    }

    /**
     * Hard disk info.
     */
    public function getDisk(): Disk
    {
        return new Disk(
            $this->os->getMounts() ?? [],
            $this->os->getDrives() ?? [],
            $this->os->getRaids() ?? []
        );
    }

    /**
     * Temperatures|Voltages.
     *
     * @return Sensor[]
     */
    public function getSensors(): array
    {
        return $this->os->getSensors() ?? [];
    }

    /**
     * Processes.
     *
     * @return Process[]
     */
    public function getProcesses(): array
    {
        return $this->os->getProcesses() ?? [];
    }

    /**
     * Services.
     *
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->os->getServices() ?? [];
    }

    /**
     * UPS status.
     */
    public function getUps(): ?Ups
    {
        return $this->os->getUps();
    }

    /**
     * Printers.
     *
     * @return Printer[]
     */
    public function getPrinters(): array
    {
        return $this->os->getPrinters() ?? [];
    }

    /**
     * Samba status.
     */
    public function getSamba(): ?Samba
    {
        return $this->os->getSamba();
    }

    /**
     * Selinux status.
     */
    public function getSelinux(): ?Selinux
    {
        return $this->os->getSelinux();
    }

    /**
     * Nginx status.
     */
    public function getNginx(?string $statusPage = null, ?string $cwd = null): ?Nginx
    {
        $data = (new Parser\WebServer\Nginx())->run($statusPage, $cwd);
        if (!$data) {
            return null;
        }

        return new Nginx(
            $data['nginx_version'],
            $data['crypto'],
            $data['tls_sni'],
            $data['args'],
            $data['status'],
        );
    }

    /**
     * Angie status.
     */
    public function getAngie(?string $statusPage = null, ?string $cwd = null): ?Angie
    {
        $data = (new Parser\WebServer\Angie())->run($statusPage, $cwd);
        if (!$data) {
            return null;
        }

        return new Angie(
            $data['angie_version'],
            $data['nginx_version'],
            $data['build_date'],
            $data['crypto'],
            $data['tls_sni'],
            $data['args'],
            $data['status'],
        );
    }

    /**
     * Apache httpd status.
     */
    public function getHttpd(?string $statusPage = null, ?string $cwd = null): ?Httpd
    {
        $data = (new Parser\WebServer\Httpd())->run($statusPage, $cwd);
        if (!$data) {
            return null;
        }

        if ($data['status']) {
            $status = new HttpdStatus(
                $data['status']['uptime'],
                $data['status']['load'],
                $data['status']['total_accesses'],
                $data['status']['total_traffic'],
                $data['status']['total_duration'],
                $data['status']['requests_sec'],
                $data['status']['b_second'],
                $data['status']['b_request'],
                $data['status']['ms_request'],
                $data['status']['requests_currently_processed'],
                $data['status']['workers_gracefully_restarting'],
                $data['status']['idle_workers'],
                $data['status']['ssl_cache_type'],
                $data['status']['ssl_shared_memory'],
            );
        } else {
            $status = null;
        }

        return new Httpd(
            $data['version'],
            $data['loaded'],
            $data['mpm'],
            $data['threaded'],
            $data['forked'],
            $data['args'],
            $status
        );
    }

    /**
     * Caddy status.
     */
    public function getCaddy(?string $configPage = null, ?string $cwd = null): ?Caddy
    {
        $data = (new Parser\WebServer\Caddy())->run($configPage, $cwd);
        if (!$data) {
            return null;
        }

        $buildInfo = new CaddyBuildInfo(
            $data['build_info']['go'],
            $data['build_info']['path'],
            $data['build_info']['mod'],
            $data['build_info']['dep'],
            $data['build_info']['build'],
        );

        return new Caddy(
            $data['version'],
            $buildInfo,
            $data['list_modules'],
            $data['config'],
        );
    }

    /**
     * Mysql/MariaDB status.
     */
    public function getMysql(\PDO $connection): ?Mysql
    {
        $data = (new Parser\Database\Mysql())->run($connection);
        if (!$data) {
            return null;
        }

        $performance95thPercentile = [];
        foreach ($data['performance_95th_percentile'] as $v) {
            $performance95thPercentile[] = new MysqlPerformance95thPercentile(
                $v['query'],
                $v['db'],
                $v['full_scan'],
                $v['exec_count'],
                $v['err_count'],
                $v['warn_count'],
                $v['total_latency'],
                $v['max_latency'],
                $v['avg_latency'],
                $v['rows_sent'],
                $v['rows_sent_avg'],
                $v['rows_examined'],
                $v['rows_examined_avg'],
                $v['first_seen'],
                $v['last_seen'],
                $v['digest'],
            );
        }

        $countQueries = [];
        foreach ($data['count_queries'] as $v) {
            $countQueries[] = new MysqlCountQueries(
                $v['object_type'],
                $v['object_schema'],
                $v['object_name'],
                $v['count_read'],
                $v['count_write'],
                $v['count_fetch'],
                $v['count_insert'],
                $v['count_update'],
                $v['count_delete'],
            );
        }

        $dataLength = [];
        foreach ($data['data_length'] as $v) {
            $dataLength[] = new MysqlDataLength(
                $v['table_schema'],
                $v['table_name'],
                $v['data_length'],
                $v['index_length'],
            );
        }

        return new Mysql(
            $data['global_status'],
            $data['global_variables'],
            $performance95thPercentile,
            $countQueries,
            $dataLength,
        );
    }

    /**
     * Redis/Valkey status.
     */
    public function getRedis(\Redis $connection): ?Redis
    {
        $data = (new Parser\Database\Redis())->run($connection);
        if (!$data) {
            return null;
        }

        return new Redis(
            $data['server'],
            $data['clients'],
            $data['memory'],
            $data['persistence'],
            $data['stats'],
            $data['replication'],
            $data['cpu'],
            $data['modules'],
            $data['errorstats'],
            $data['cluster'],
            $data['keyspace'],
            $data['keysizes'],
        );
    }

    /**
     * Manticore status.
     */
    public function getManticore(\PDO $connection): ?Manticore
    {
        $data = (new Parser\Database\Manticore())->run($connection);
        if (!$data) {
            return null;
        }

        return new Manticore(
            $data['global_variables'],
            $data['status'],
            $data['settings'],
            $data['agent_status'],
        );
    }

    /**
     * Postgres status.
     */
    public function getPostgres(\PDO $connection): ?Postgres
    {
        $data = (new Parser\Database\Postgres())->run($connection);
        if (!$data) {
            return null;
        }

        $pgStatActivity = [];
        foreach ($data['pg_stat_activity'] as $v) {
            $pgStatActivity[] = new PostgresPgStatActivity(
                $v['datid'],
                $v['datname'],
                $v['pid'],
                $v['leader_pid'],
                $v['usesysid'],
                $v['usename'],
                $v['application_name'],
                $v['client_addr'],
                $v['client_hostname'],
                $v['client_port'],
                $v['backend_start'],
                $v['xact_start'],
                $v['query_start'],
                $v['state_change'],
                $v['wait_event_type'],
                $v['wait_event'],
                $v['state'],
                $v['backend_xid'],
                $v['backend_xmin'],
                $v['query_id'],
                $v['query'],
                $v['backend_type'],
            );
        }

        $pgStatDatabase = [];
        foreach ($data['pg_stat_database'] as $v) {
            $pgStatDatabase[] = new PostgresPgStatDatabase(
                $v['datid'],
                $v['datname'],
                $v['numbackends'],
                $v['xact_commit'],
                $v['xact_rollback'],
                $v['blks_read'],
                $v['blks_hit'],
                $v['tup_returned'],
                $v['tup_fetched'],
                $v['tup_inserted'],
                $v['tup_updated'],
                $v['tup_deleted'],
                $v['conflicts'],
                $v['temp_files'],
                $v['temp_bytes'],
                $v['deadlocks'],
                $v['checksum_failures'],
                $v['checksum_last_failure'],
                $v['blk_read_time'],
                $v['blk_write_time'],
                $v['session_time'],
                $v['active_time'],
                $v['idle_in_transaction_time'],
                $v['sessions'],
                $v['sessions_abandoned'],
                $v['sessions_fatal'],
                $v['sessions_killed'],
                $v['stats_reset'],
            );
        }

        $pgStatAllTables = [];
        foreach ($data['pg_stat_all_tables'] as $v) {
            $pgStatAllTables[] = new PostgresPgStatAllTables(
                $v['relid'],
                $v['schemaname'],
                $v['relname'],
                $v['seq_scan'],
                $v['last_seq_scan'],
                $v['seq_tup_read'],
                $v['idx_scan'],
                $v['last_idx_scan'],
                $v['idx_tup_fetch'],
                $v['n_tup_ins'],
                $v['n_tup_upd'],
                $v['n_tup_del'],
                $v['n_tup_hot_upd'],
                $v['n_tup_newpage_upd'],
                $v['n_live_tup'],
                $v['n_dead_tup'],
                $v['n_mod_since_analyze'],
                $v['n_ins_since_vacuum'],
                $v['last_vacuum'],
                $v['last_autovacuum'],
                $v['last_analyze'],
                $v['last_autoanalyze'],
                $v['vacuum_count'],
                $v['autovacuum_count'],
                $v['analyze_count'],
                $v['autoanalyze_count'],
            );
        }

        $pgStatAllIndexes = [];
        foreach ($data['pg_stat_all_indexes'] as $v) {
            $pgStatAllIndexes[] = new PostgresPgStatAllIndexes(
                $v['relid'],
                $v['indexrelid'],
                $v['schemaname'],
                $v['relname'],
                $v['indexrelname'],
                $v['idx_scan'],
                $v['last_idx_scan'],
                $v['idx_tup_read'],
                $v['idx_tup_fetch'],
            );
        }

        $pgStatStatements = [];
        foreach ($data['pg_stat_statements'] as $v) {
            $pgStatStatements[] = new PostgresPgStatStatements(
                $v['userid'],
                $v['dbid'],
                $v['toplevel'],
                $v['queryid'],
                $v['query'],
                $v['plans'],
                $v['total_plan_time'],
                $v['min_plan_time'],
                $v['max_plan_time'],
                $v['mean_plan_time'],
                $v['stddev_plan_time'],
                $v['calls'],
                $v['total_exec_time'],
                $v['min_exec_time'],
                $v['max_exec_time'],
                $v['mean_exec_time'],
                $v['stddev_exec_time'],
                $v['rows'],
                $v['shared_blks_hit'],
                $v['shared_blks_read'],
                $v['shared_blks_dirtied'],
                $v['shared_blks_written'],
                $v['local_blks_hit'],
                $v['local_blks_read'],
                $v['local_blks_dirtied'],
                $v['local_blks_written'],
                $v['temp_blks_read'],
                $v['temp_blks_written'],
                $v['shared_blk_read_time'],
                $v['shared_blk_write_time'],
                $v['local_blk_read_time'],
                $v['local_blk_write_time'],
                $v['temp_blk_read_time'],
                $v['temp_blk_write_time'],
                $v['wal_records'],
                $v['wal_fpi'],
                $v['wal_bytes'],
                $v['jit_functions'],
                $v['jit_generation_time'],
                $v['jit_inlining_count'],
                $v['jit_inlining_time'],
                $v['jit_optimization_count'],
                $v['jit_optimization_time'],
                $v['jit_emission_count'],
                $v['jit_emission_time'],
                $v['jit_deform_count'],
                $v['jit_deform_time'],
                $v['stats_since'],
                $v['minmax_stats_since'],
            );
        }

        return new Postgres(
            $data['version'],
            $pgStatActivity,
            $pgStatDatabase,
            $pgStatAllTables,
            $pgStatAllIndexes,
            $pgStatStatements,
        );
    }

    /**
     * Sqlite status.
     */
    public function getSqlite(\PDO $connection): ?Sqlite
    {
        $data = (new Parser\Database\Sqlite())->run($connection);
        if (!$data) {
            return null;
        }

        $pragmaTableList = [];
        foreach ($data['pragma']['table_list'] as $v) {
            $pragmaTableList[] = new SqlitePragmaTable(
                $v['schema'],
                $v['name'],
                $v['type'],
                $v['ncol'],
                $v['wr'],
                $v['strict'],
            );
        }

        $pragma = new SqlitePragma(
            $data['pragma']['auto_vacuum'],
            $data['pragma']['automatic_index'],
            $data['pragma']['busy_timeout'],
            $data['pragma']['cache_size'],
            $data['pragma']['encoding'],
            $data['pragma']['ignore_check_constraints'],
            $data['pragma']['incremental_vacuum'],
            $data['pragma']['journal_mode'],
            $data['pragma']['journal_size_limit'],
            $data['pragma']['locking_mode'],
            $data['pragma']['page_count'],
            $data['pragma']['page_size'],
            $data['pragma']['quick_check'],
            $data['pragma']['read_uncommitted'],
            $data['pragma']['secure_delete'],
            $data['pragma']['synchronous'],
            $data['pragma']['threads'],
            $data['pragma']['trusted_schema'],
            $data['pragma']['wal_autocheckpoint'],
            $data['pragma']['collation_list'],
            $data['pragma']['compile_options'],
            $pragmaTableList,
        );

        return new Sqlite(
            $data['sqlite_version'],
            $data['sqlite_source_id'],
            $data['db_size'],
            $pragma,
        );
    }

    public function getPhp(): Php
    {
        $opcacheStatus = \function_exists('opcache_get_status') ? \opcache_get_status(false) : null;
        $opcacheConfiguration = \function_exists('opcache_get_configuration') ? \opcache_get_configuration() : null;

        $apcuCacheInfo = \function_exists('apcu_cache_info') ? @\apcu_cache_info(true) : null; // suppressing  errors for cli and some other reasons
        $apcuSmaInfo = \function_exists('apcu_sma_info') ? @\apcu_sma_info(true) : null; // suppressing  errors for cli and some other reasons

        $fpmInfo = \function_exists('fpm_get_status') ? \fpm_get_status() : null;

        $disabledFunctions = \ini_get('disable_functions');
        $disabledClasses = \ini_get('disable_classes');

        $apcEnabled = \ini_get('apc.enabled');
        $apcEnableCli = \ini_get('apc.enable_cli');

        $opcache = new Php\Opcache(
            $opcacheStatus['opcache_enabled'] ?? false,
            \phpversion('Zend Opcache') ?: null,
            $opcacheConfiguration['directives']['opcache.enable'] ?? false,
            $opcacheConfiguration['directives']['opcache.enable_cli'] ?? null,
            $opcacheStatus['memory_usage']['used_memory'] ?? null,
            $opcacheStatus['memory_usage']['free_memory'] ?? null,
            $opcacheStatus['opcache_statistics']['num_cached_scripts'] ?? null,
            $opcacheStatus['opcache_statistics']['hits'] ?? null,
            $opcacheStatus['opcache_statistics']['misses'] ?? null,
            $opcacheStatus['interned_strings_usage']['used_memory'] ?? null,
            $opcacheStatus['interned_strings_usage']['free_memory'] ?? null,
            $opcacheStatus['interned_strings_usage']['number_of_strings'] ?? null,
            $opcacheStatus['opcache_statistics']['oom_restarts'] ?? null,
            $opcacheStatus['opcache_statistics']['hash_restarts'] ?? null,
            $opcacheStatus['opcache_statistics']['manual_restarts'] ?? null
        );

        $apcu = new Php\Apcu(
            $apcuCacheInfo && $apcuSmaInfo,
            \phpversion('apcu') ?: null,
            false !== $apcEnabled ? (bool) $apcEnabled : null,
            false !== $apcEnableCli ? (bool) $apcEnableCli : null,
            $apcuCacheInfo['num_hits'] ?? null,
            $apcuCacheInfo['num_misses'] ?? null,
            isset($apcuSmaInfo['num_seg'], $apcuSmaInfo['seg_size'], $apcuSmaInfo['avail_mem']) ? $apcuSmaInfo['num_seg'] * $apcuSmaInfo['seg_size'] - $apcuSmaInfo['avail_mem'] : null,
            $apcuSmaInfo['avail_mem'] ?? null,
            $apcuCacheInfo['num_entries'] ?? null
        );

        $processes = isset($fpmInfo['procs']) ? \array_map(static function (array $process): Php\FpmProcess {
            return new Php\FpmProcess(
                $process['pid'],
                $process['state'],
                new \DateTimeImmutable('@'.$process['start-time']),
                $process['requests'],
                $process['request-duration'],
                $process['request-method'],
                $process['request-uri'],
                $process['query-string'],
                $process['request-length'],
                $process['user'],
                $process['script'],
                $process['last-request-cpu'],
                $process['last-request-memory'],
            );
        }, $fpmInfo['procs']) : [];

        $fpm = new Php\Fpm(
            !empty($fpmInfo),
            $fpmInfo['pool'] ?? null,
            $fpmInfo['process-manager'] ?? null,
            isset($fpmInfo['start-time']) ? new \DateTimeImmutable('@'.$fpmInfo['start-time']) : null,
            $fpmInfo['accepted-conn'] ?? null,
            $fpmInfo['listen-queue'] ?? null,
            $fpmInfo['max-listen-queue'] ?? null,
            $fpmInfo['listen-queue-len'] ?? null,
            $fpmInfo['idle-processes'] ?? null,
            $fpmInfo['active-processes'] ?? null,
            $fpmInfo['max-active-processes'] ?? null,
            $fpmInfo['max-children-reached'] ?? null,
            $fpmInfo['slow-requests'] ?? null,
            $fpmInfo['memory-peak'] ?? null,
            $processes
        );

        return new Php(
            \PHP_VERSION,
            \PHP_SAPI,
            \ZEND_THREAD_SAFE,
            (int) Common::convertHumanSizeToBytes((string) \ini_get('memory_limit')),
            \get_loaded_extensions(),
            \get_loaded_extensions(true),
            (string) \php_ini_loaded_file(),
            (string) \get_include_path(),
            (string) \ini_get('open_basedir'),
            $disabledFunctions ? \explode(',', $disabledFunctions) : [],
            $disabledClasses ? \explode(',', $disabledClasses) : [],
            $opcache,
            $apcu,
            $fpm,
            \realpath_cache_size(),
            Common::convertHumanSizeToBytes((string) \ini_get('realpath_cache_size')),
        );
    }
}
