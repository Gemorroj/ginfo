<?php

namespace Ginfo\Info\Php;

class Apcu
{
    /** @var string|null */
    private $version;
    /** @var bool */
    private $enabled;
    /** @var bool|null */
    private $configEnable;
    /** @var bool|null */
    private $configEnableCli;
    /** @var int|null */
    private $hits;
    /** @var int|null */
    private $misses;
    /** @var int|null */
    private $usedMemory;
    /** @var int|null */
    private $freeMemory;
    /** @var int|null */
    private $cachedVariables;

    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @return $this
     */
    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfigEnable(): ?bool
    {
        return $this->configEnable;
    }

    /**
     * @return $this
     */
    public function setConfigEnable(?bool $configEnable): self
    {
        $this->configEnable = $configEnable;

        return $this;
    }

    public function getConfigEnableCli(): ?bool
    {
        return $this->configEnableCli;
    }

    /**
     * @return $this
     */
    public function setConfigEnableCli(?bool $configEnableCli): self
    {
        $this->configEnableCli = $configEnableCli;

        return $this;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    /**
     * @return $this
     */
    public function setHits(?int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    public function getMisses(): ?int
    {
        return $this->misses;
    }

    /**
     * @return $this
     */
    public function setMisses(?int $misses): self
    {
        $this->misses = $misses;

        return $this;
    }

    public function getUsedMemory(): ?int
    {
        return $this->usedMemory;
    }

    /**
     * @return $this
     */
    public function setUsedMemory(?int $usedMemory): self
    {
        $this->usedMemory = $usedMemory;

        return $this;
    }

    public function getFreeMemory(): ?int
    {
        return $this->freeMemory;
    }

    /**
     * @return $this
     */
    public function setFreeMemory(?int $freeMemory): self
    {
        $this->freeMemory = $freeMemory;

        return $this;
    }

    public function getCachedVariables(): ?int
    {
        return $this->cachedVariables;
    }

    /**
     * @return $this
     */
    public function setCachedVariables(?int $cachedVariables): self
    {
        $this->cachedVariables = $cachedVariables;

        return $this;
    }
}
