<?php

namespace Ginfo\Info\Php;

class Opcache
{
    /** @var bool */
    private $enabled;
    /** @var bool|null */
    private $configEnable;
    /** @var bool|null */
    private $configEnableCli;
    /** @var int|null */
    private $usedMemory;
    /** @var int|null */
    private $freeMemory;
    /** @var int|null */
    private $cachedScripts;
    /** @var int|null */
    private $hits;
    /** @var int|null */
    private $misses;

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getConfigEnable(): ?bool
    {
        return $this->configEnable;
    }

    /**
     * @param bool|null $configEnable
     * @return $this
     */
    public function setConfigEnable(?bool $configEnable): self
    {
        $this->configEnable = $configEnable;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getConfigEnableCli(): ?bool
    {
        return $this->configEnableCli;
    }

    /**
     * @param bool|null $configEnableCli
     * @return $this
     */
    public function setConfigEnableCli(?bool $configEnableCli): self
    {
        $this->configEnableCli = $configEnableCli;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUsedMemory(): ?int
    {
        return $this->usedMemory;
    }

    /**
     * @param int|null $usedMemory
     * @return $this
     */
    public function setUsedMemory(?int $usedMemory): self
    {
        $this->usedMemory = $usedMemory;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFreeMemory(): ?int
    {
        return $this->freeMemory;
    }

    /**
     * @param int|null $freeMemory
     * @return $this
     */
    public function setFreeMemory(?int $freeMemory): self
    {
        $this->freeMemory = $freeMemory;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCachedScripts(): ?int
    {
        return $this->cachedScripts;
    }

    /**
     * @param int|null $cachedScripts
     * @return $this
     */
    public function setCachedScripts(?int $cachedScripts): self
    {
        $this->cachedScripts = $cachedScripts;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHits(): ?int
    {
        return $this->hits;
    }

    /**
     * @param int|null $hits
     * @return $this
     */
    public function setHits(?int $hits): self
    {
        $this->hits = $hits;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMisses(): ?int
    {
        return $this->misses;
    }

    /**
     * @param int|null $misses
     * @return $this
     */
    public function setMisses(?int $misses): self
    {
        $this->misses = $misses;
        return $this;
    }
}
