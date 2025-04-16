<?php

namespace Ginfo\Info\WebServer;

final readonly class CaddyBuildInfo
{
    /**
     * @param string[] $dep
     * @param string[] $build
     */
    public function __construct(
        private string $go,
        private string $path,
        private string $mod,
        private array $dep,
        private array $build,
    ) {
    }

    public function getGo(): string
    {
        return $this->go;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMod(): string
    {
        return $this->mod;
    }

    /**
     * @return string[]
     */
    public function getDep(): array
    {
        return $this->dep;
    }

    /**
     * @return string[]
     */
    public function getBuild(): array
    {
        return $this->build;
    }
}
