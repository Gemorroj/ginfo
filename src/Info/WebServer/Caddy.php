<?php

namespace Ginfo\Info\WebServer;

final readonly class Caddy
{
    /**
     * @param string[] $listModules
     */
    public function __construct(
        private string $version,
        private CaddyBuildInfo $buildInfo,
        private array $listModules,
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

    public function getConfig(): ?array
    {
        return $this->config;
    }
}
