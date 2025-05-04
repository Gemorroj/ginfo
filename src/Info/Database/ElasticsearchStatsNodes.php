<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class ElasticsearchStatsNodes implements InfoInterface
{
    /**
     * @param string[] $versions
     * @param string[] $jvmVersions
     * @param string[] $plugins
     */
    public function __construct(
        private int $count,
        private array $versions,
        private int $availableProcessors,
        private int $allocatedProcessors,
        private int|float $memTotalInBytes,
        private int|float $memFreeInBytes,
        private int|float $memUsedInBytes,
        private int $memFreePercent,
        private int $memUsedPercent,
        private int $processCpuPercent,
        private int $processOpenFileDescriptorsMin,
        private int $processOpenFileDescriptorsMax,
        private int $processOpenFileDescriptorsAvg,
        private array $jvmVersions,
        private int|float $jvmMemHeapUsedInBytes,
        private int|float $jvmMemHeapMaxInBytes,
        private int $jvmThreads,
        private int|float $fsTotalInBytes,
        private int|float $fsFreeInBytes,
        private int|float $fsAvailableInBytes,
        private int|float $fsCacheReservedInBytes,
        private array $plugins,
    ) {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getVersions(): array
    {
        return $this->versions;
    }

    public function getAvailableProcessors(): int
    {
        return $this->availableProcessors;
    }

    public function getAllocatedProcessors(): int
    {
        return $this->allocatedProcessors;
    }

    public function getMemTotalInBytes(): float|int
    {
        return $this->memTotalInBytes;
    }

    public function getMemFreeInBytes(): float|int
    {
        return $this->memFreeInBytes;
    }

    public function getMemUsedInBytes(): float|int
    {
        return $this->memUsedInBytes;
    }

    public function getMemFreePercent(): int
    {
        return $this->memFreePercent;
    }

    public function getMemUsedPercent(): int
    {
        return $this->memUsedPercent;
    }

    public function getProcessCpuPercent(): int
    {
        return $this->processCpuPercent;
    }

    public function getProcessOpenFileDescriptorsMin(): int
    {
        return $this->processOpenFileDescriptorsMin;
    }

    public function getProcessOpenFileDescriptorsMax(): int
    {
        return $this->processOpenFileDescriptorsMax;
    }

    public function getProcessOpenFileDescriptorsAvg(): int
    {
        return $this->processOpenFileDescriptorsAvg;
    }

    public function getJvmVersions(): array
    {
        return $this->jvmVersions;
    }

    public function getJvmMemHeapUsedInBytes(): float|int
    {
        return $this->jvmMemHeapUsedInBytes;
    }

    public function getJvmMemHeapMaxInBytes(): float|int
    {
        return $this->jvmMemHeapMaxInBytes;
    }

    public function getJvmThreads(): int
    {
        return $this->jvmThreads;
    }

    public function getFsTotalInBytes(): float|int
    {
        return $this->fsTotalInBytes;
    }

    public function getFsFreeInBytes(): float|int
    {
        return $this->fsFreeInBytes;
    }

    public function getFsAvailableInBytes(): float|int
    {
        return $this->fsAvailableInBytes;
    }

    public function getFsCacheReservedInBytes(): float|int
    {
        return $this->fsCacheReservedInBytes;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
