<?php

namespace Ginfo\Info\WebServer;

use Ginfo\Info\InfoInterface;

final readonly class Caddy implements InfoInterface
{
    /**
     * @param string[]       $listModules
     * @param CaddyProcess[] $processes
     */
    public function __construct(
        private string $version,
        private CaddyBuildInfo $buildInfo,
        private array $listModules,
        private array $processes,
        private ?array $config = null,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getBuildInfo(): CaddyBuildInfo
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
     * @return CaddyProcess[]
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }
}
