<?php

namespace Ginfo\Info\Php;

class Apcu
{
    private ?string $version = null;
    private bool $enabled;
    private ?bool $configEnable = null;
    private ?bool $configEnableCli = null;
    private ?int $hits = null;
    private ?int $misses = null;
    private ?int $usedMemory = null;
    private ?int $freeMemory = null;
    private ?int $cachedVariables = null;

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfigEnable(): ?bool
    {
        return $this->configEnable;
    }

    public function setConfigEnable(?bool $configEnable): self
    {
        $this->configEnable = $configEnable;

        return $this;
    }

    public function getConfigEnableCli(): ?bool
    {
        return $this->configEnableCli;
    }

    public function setConfigEnableCli(?bool $configEnableCli): self
    {
        $this->configEnableCli = $configEnableCli;

        return $this;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function setHits(?int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    public function getMisses(): ?int
    {
        return $this->misses;
    }

    public function setMisses(?int $misses): self
    {
        $this->misses = $misses;

        return $this;
    }

    public function getUsedMemory(): ?int
    {
        return $this->usedMemory;
    }

    public function setUsedMemory(?int $usedMemory): self
    {
        $this->usedMemory = $usedMemory;

        return $this;
    }

    public function getFreeMemory(): ?int
    {
        return $this->freeMemory;
    }

    public function setFreeMemory(?int $freeMemory): self
    {
        $this->freeMemory = $freeMemory;

        return $this;
    }

    public function getCachedVariables(): ?int
    {
        return $this->cachedVariables;
    }

    public function setCachedVariables(?int $cachedVariables): self
    {
        $this->cachedVariables = $cachedVariables;

        return $this;
    }
}
