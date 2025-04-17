<?php

namespace Ginfo;

use Ginfo\Exception\UnknownParserException;
use Ginfo\Info\Battery;
use Ginfo\Info\Cpu;
use Ginfo\Info\Database\Mysql;
use Ginfo\Info\Database\MysqlPerformance;
use Ginfo\Info\Database\MysqlSummary;
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
use Ginfo\Os\OsInterface;

final readonly class Info
{
    /**
     * @var InfoParserInterface[]
     */
    private array $customParsers;

    public function __construct(private OsInterface $os, InfoParserInterface ...$customParser)
    {
        $this->customParsers = $customParser;
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
     * Mysql status.
     */
    public function getMysql(\PDO $connection, bool $summary = true): ?Mysql
    {
        $data = (new Parser\Database\Mysql())->run($connection, $summary);
        if (!$data) {
            return null;
        }

        $performanceData = [];
        foreach ($data['performance'] as $v) {
            $performanceData[] = new MysqlPerformance(
                $v['schema_name'],
                $v['count'],
                $v['avg_microsec'],
            );
        }

        $summaryData = [];
        foreach ($data['summary'] as $v) {
            $summaryData[] = new MysqlSummary(
                $v['host'],
                $v['statement'],
                $v['total'],
                $v['total_latency'],
                $v['max_latency'],
                $v['lock_latency'],
                $v['cpu_latency'],
                $v['rows_sent'],
                $v['rows_examined'],
                $v['rows_affected'],
                $v['full_scans'],
            );
        }

        return new Mysql(
            $data['global_status'],
            $data['variables'],
            $performanceData,
            $summaryData,
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
