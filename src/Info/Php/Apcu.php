<?php

namespace Ginfo\Info\Php;

final readonly class Apcu
{
    public function __construct(
        private bool $enabled,
        private ?string $version = null,
        private ?bool $configEnable = null,
        private ?bool $configEnableCli = null,
        private ?int $hits = null,
        private ?int $misses = null,
        private ?int $usedMemory = null,
        private ?int $freeMemory = null,
        private ?int $cachedVariables = null
    ) {
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getConfigEnable(): ?bool
    {
        return $this->configEnable;
    }

    public function getConfigEnableCli(): ?bool
    {
        return $this->configEnableCli;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function getMisses(): ?int
    {
        return $this->misses;
    }

    public function getUsedMemory(): ?int
    {
        return $this->usedMemory;
    }

    public function getFreeMemory(): ?int
    {
        return $this->freeMemory;
    }

    public function getCachedVariables(): ?int
    {
        return $this->cachedVariables;
    }
}
