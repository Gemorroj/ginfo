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
    /** @var string|null */
    private $version;
    /** @var int|null */
    private $internedStringsUsedMemory;
    /** @var int|null */
    private $internedStringsFreeMemory;
    /** @var int|null */
    private $cachedInternedStrings;

    public function getInternedStringsUsedMemory(): ?int
    {
        return $this->internedStringsUsedMemory;
    }

    /**
     * @return $this
     */
    public function setInternedStringsUsedMemory(?int $internedStringsUsedMemory): self
    {
        $this->internedStringsUsedMemory = $internedStringsUsedMemory;

        return $this;
    }

    public function getInternedStringsFreeMemory(): ?int
    {
        return $this->internedStringsFreeMemory;
    }

    /**
     * @return $this
     */
    public function setInternedStringsFreeMemory(?int $internedStringsFreeMemory): self
    {
        $this->internedStringsFreeMemory = $internedStringsFreeMemory;

        return $this;
    }

    public function getCachedInternedStrings(): ?int
    {
        return $this->cachedInternedStrings;
    }

    /**
     * @return $this
     */
    public function setCachedInternedStrings(?int $cachedInternedStrings): self
    {
        $this->cachedInternedStrings = $cachedInternedStrings;

        return $this;
    }

    /**
     * @var int|null
     *               number of restarts because of out of memory
     */
    private $oomRestarts;
    /**
     * @var int|null
     *               number of restarts because of hash overflow
     */
    private $hashRestarts;
    /**
     * @var int|null
     *               number of restarts scheduled by opcache_reset()
     */
    private $manualRestarts;

    public function getOomRestarts(): ?int
    {
        return $this->oomRestarts;
    }

    /**
     * @return $this
     */
    public function setOomRestarts(?int $oomRestarts): self
    {
        $this->oomRestarts = $oomRestarts;

        return $this;
    }

    public function getHashRestarts(): ?int
    {
        return $this->hashRestarts;
    }

    /**
     * @return $this
     */
    public function setHashRestarts(?int $hashRestarts): self
    {
        $this->hashRestarts = $hashRestarts;

        return $this;
    }

    public function getManualRestarts(): ?int
    {
        return $this->manualRestarts;
    }

    /**
     * @return $this
     */
    public function setManualRestarts(?int $manualRestarts): self
    {
        $this->manualRestarts = $manualRestarts;

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

    public function getCachedScripts(): ?int
    {
        return $this->cachedScripts;
    }

    /**
     * @return $this
     */
    public function setCachedScripts(?int $cachedScripts): self
    {
        $this->cachedScripts = $cachedScripts;

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
}
