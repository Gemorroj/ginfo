<?php

namespace Ginfo\Info\WebServer;

use Ginfo\Info\InfoInterface;

final readonly class Frankenphp implements InfoInterface
{
    /**
     * @param string[]            $listModules
     * @param FrankenphpProcess[] $processes
     * @param FrankenphpThread[]  $threads
     */
    public function __construct(
        private string $version,
        private FrankenphpBuildInfo $buildInfo,
        private array $listModules,
        private array $processes,
        private array $threads,
        private int $reservedThreadCount,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getBuildInfo(): FrankenphpBuildInfo
    {
        return $this->buildInfo;
    }

    /**
     * @return string[]
     */
    public function getListModules(): array
    {
        return $this->listModules;
    }

    /**
     * @return FrankenphpProcess[]
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }

    /**
     * @return FrankenphpThread[] Empty when the admin API is unavailable
     */
    public function getThreads(): array
    {
        return $this->threads;
    }

    /**
     * @return int 0 when the admin API is unavailable
     */
    public function getReservedThreadCount(): int
    {
        return $this->reservedThreadCount;
    }
}
