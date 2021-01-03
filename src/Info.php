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
use Ginfo\OS\OS;

class Info
{
    /** @var OS */
    private $os;

    public function __construct(OS $os)
    {
        $this->os = $os;
    }

    /**
     * General info.
     */
    public function getGeneral(): General
    {
        return (new General())
            ->setDate(new \DateTime())
            ->setUptime($this->os->getUptime())
            ->setOsName($this->os->getOsName())
            ->setKernel($this->os->getKernel())
            ->setHostname($this->os->getHostName())
            ->setArchitecture($this->os->getArchitecture())
            ->setVirtualization($this->os->getVirtualization())
            ->setModel($this->os->getModel())
            ->setLoggedUsers($this->os->getLoggedUsers())
            ->setLoad($this->os->getLoad());
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
     * @return Usb[]|null
     */
    public function getUsb(): ?array
    {
        return $this->os->getUsb();
    }

    /**
     * PCI devices.
     *
     * @return Pci[]|null
     */
    public function getPci(): ?array
    {
        return $this->os->getPci();
    }

    /**
     * Sound cards.
     *
     * @return SoundCard[]|null
     */
    public function getSoundCard(): ?array
    {
        return $this->os->getSoundCards();
    }

    /**
     * Network devices.
     *
     * @return Network[]|null
     */
    public function getNetwork(): ?array
    {
        return $this->os->getNetwork();
    }

    /**
     * Battery status.
     *
     * @return Battery[]|null
     */
    public function getBattery(): ?array
    {
        return $this->os->getBattery();
    }

    /**
     * Hard disk info.
     */
    public function getDisk(): Disk
    {
        return (new Disk())
            ->setMounts($this->os->getMounts())
            ->setDrives($this->os->getDrives())
            ->setRaids($this->os->getRaids());
    }

    /**
     * Temperatures|Voltages.
     *
     * @return Sensor[]|null
     */
    public function getSensors(): ?array
    {
        return $this->os->getSensors();
    }

    /**
     * Processes.
     *
     * @return Process[]|null
     */
    public function getProcesses(): ?array
    {
        return $this->os->getProcesses();
    }

    /**
     * Services.
     *
     * @return Service[]|null
     */
    public function getServices(): ?array
    {
        return $this->os->getServices();
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
     * @return Printer[]|null
     */
    public function getPrinters(): ?array
    {
        return $this->os->getPrinters();
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

        $apcuCacheInfo = \function_exists('apcu_cache_info') ? @\apcu_cache_info(true) : null;
        $apcuSmaInfo = \function_exists('apcu_sma_info') ? @\apcu_sma_info(true) : null;

        $fpmInfo = \function_exists('fpm_get_status') ? \fpm_get_status() : null;

        $disabledFunctions = \ini_get('disable_functions');
        $disabledClasses = \ini_get('disable_classes');

        return (new Php())
            ->setVersion(\PHP_VERSION)
            ->setExtensions(\get_loaded_extensions())
            ->setZendExtensions(\get_loaded_extensions(true))
            ->setIniFile(\php_ini_loaded_file())
            ->setMemoryLimit(Common::convertHumanSizeToBytes(\ini_get('memory_limit')))
            ->setIncludePath(\get_include_path())
            ->setOpenBasedir(\ini_get('open_basedir'))
            ->setZendThreadSafe(\ZEND_THREAD_SAFE)
            ->setSapiName(\PHP_SAPI)
            ->setDisabledFunctions($disabledFunctions ? \explode(',', $disabledFunctions) : [])
            ->setDisabledClasses($disabledClasses ? \explode(',', $disabledClasses) : [])
            ->setRealpathCacheSizeUsed(\realpath_cache_size())
            ->setRealpathCacheSizeAllowed(Common::convertHumanSizeToBytes(\ini_get('realpath_cache_size')))
            ->setOpcache(
                (new Php\Opcache())
                    ->setVersion(\phpversion('Zend Opcache') ?: null)
                    ->setCachedScripts($opcacheStatus['opcache_statistics']['num_cached_scripts'] ?? null)
                    ->setConfigEnable($opcacheConfiguration['directives']['opcache.enable'] ?? false)
                    ->setConfigEnableCli($opcacheConfiguration['directives']['opcache.enable_cli'] ?? null)
                    ->setEnabled($opcacheStatus['opcache_enabled'] ?? false)
                    ->setFreeMemory($opcacheStatus['memory_usage']['free_memory'] ?? null)
                    ->setUsedMemory($opcacheStatus['memory_usage']['used_memory'] ?? null)
                    ->setHits($opcacheStatus['opcache_statistics']['hits'] ?? null)
                    ->setMisses($opcacheStatus['opcache_statistics']['misses'] ?? null)
                    ->setOomRestarts($opcacheStatus['opcache_statistics']['oom_restarts'] ?? null)
                    ->setHashRestarts($opcacheStatus['opcache_statistics']['hash_restarts'] ?? null)
                    ->setHashRestarts($opcacheStatus['opcache_statistics']['manual_restarts'] ?? null)
                    ->setCachedInternedStrings($opcacheStatus['interned_strings_usage']['number_of_strings'] ?? null)
                    ->setInternedStringsFreeMemory($opcacheStatus['interned_strings_usage']['free_memory'] ?? null)
                    ->setInternedStringsUsedMemory($opcacheStatus['interned_strings_usage']['used_memory'] ?? null)
            )
            ->setApcu(
                (new Php\Apcu())
                    ->setVersion(\phpversion('apcu') ?: null)
                    ->setCachedVariables($apcuCacheInfo['num_entries'] ?? null)
                    ->setConfigEnable(false !== \ini_get('apc.enabled') ? \ini_get('apc.enabled') : null)
                    ->setConfigEnableCli(false !== \ini_get('apc.enable_cli') ? \ini_get('apc.enable_cli') : null)
                    ->setEnabled($apcuCacheInfo && $apcuSmaInfo)
                    ->setFreeMemory($apcuSmaInfo['avail_mem'] ?? null)
                    ->setUsedMemory(isset($apcuSmaInfo['num_seg'], $apcuSmaInfo['seg_size'], $apcuSmaInfo['avail_mem']) ? $apcuSmaInfo['num_seg'] * $apcuSmaInfo['seg_size'] - $apcuSmaInfo['avail_mem'] : null)
                    ->setHits($apcuCacheInfo['num_hits'] ?? null)
                    ->setMisses($apcuCacheInfo['num_misses'] ?? null)
            )
            ->setFpm(
                (new Php\Fpm())
                    ->setEnabled(!empty($fpmInfo))
                    ->setAcceptedConnections($fpmInfo['accepted-conn'] ?? null)
                    ->setActiveProcesses($fpmInfo['active-processes'] ?? null)
                    ->setIdleProcesses($fpmInfo['idle-processes'] ?? null)
                    ->setListenQueue($fpmInfo['listen-queue'] ?? null)
                    ->setListenQueueLength($fpmInfo['listen-queue-len'] ?? null)
                    ->setMaxActiveProcesses($fpmInfo['max-active-processes'] ?? null)
                    ->setMaxChildrenReached($fpmInfo['max-children-reached'] ?? null)
                    ->setMaxListenQueue($fpmInfo['max-listen-queue'] ?? null)
                    ->setPool($fpmInfo['pool'] ?? null)
                    ->setProcessManager($fpmInfo['process-manager'] ?? null)
                    ->setSlowRequests($fpmInfo['slow-requests'] ?? null)
                    ->setStartTime(isset($fpmInfo['start-time']) ? new \DateTime('@'.$fpmInfo['start-time']) : null)
                    ->setProcesses(isset($fpmInfo['procs']) ? \array_map(static function (array $process): Php\FpmProcess {
                        return (new Php\FpmProcess())
                            ->setStartTime(new \DateTime('@'.$process['start-time']))
                            ->setLastRequestCpu($process['last-request-cpu'])
                            ->setLastRequestMemory($process['last-request-memory'])
                            ->setPid($process['pid'])
                            ->setQueryString($process['query-string'])
                            ->setRequestDuration($process['request-duration'])
                            ->setRequestLength($process['request-length'])
                            ->setRequestMethod($process['request-method'])
                            ->setRequests($process['requests'])
                            ->setRequestUri($process['request-uri'])
                            ->setScript($process['script'])
                            ->setState($process['state'])
                            ->setUser($process['user'])
                            ;
                    }, $fpmInfo['procs']) : null)
            );
    }
}
