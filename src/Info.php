<?php

namespace Ginfo;

use Ginfo\Info\Battery;
use Ginfo\Info\Cpu;
use Ginfo\Info\Disk;
use Ginfo\Info\General;
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
use Ginfo\Os\OsInterface;

final readonly class Info
{
    public function __construct(private OsInterface $os)
    {
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
