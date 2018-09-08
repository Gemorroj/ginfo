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

    /**
     * @return int|null
     */
    public function getInternedStringsUsedMemory(): ?int
    {
        return $this->internedStringsUsedMemory;
    }

    /**
     * @param int|null $internedStringsUsedMemory
     *
     * @return $this
     */
    public function setInternedStringsUsedMemory(?int $internedStringsUsedMemory): self
    {
        $this->internedStringsUsedMemory = $internedStringsUsedMemory;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInternedStringsFreeMemory(): ?int
    {
        return $this->internedStringsFreeMemory;
    }

    /**
     * @param int|null $internedStringsFreeMemory
     *
     * @return $this
     */
    public function setInternedStringsFreeMemory(?int $internedStringsFreeMemory): self
    {
        $this->internedStringsFreeMemory = $internedStringsFreeMemory;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCachedInternedStrings(): ?int
    {
        return $this->cachedInternedStrings;
    }

    /**
     * @param int|null $cachedInternedStrings
     *
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

    /**
     * @return int|null
     */
    public function getOomRestarts(): ?int
    {
        return $this->oomRestarts;
    }

    /**
     * @param int|null $oomRestarts
     *
     * @return $this
     */
    public function setOomRestarts(?int $oomRestarts): self
    {
        $this->oomRestarts = $oomRestarts;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHashRestarts(): ?int
    {
        return $this->hashRestarts;
    }

    /**
     * @param int|null $hashRestarts
     *
     * @return $this
     */
    public function setHashRestarts(?int $hashRestarts): self
    {
        $this->hashRestarts = $hashRestarts;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getManualRestarts(): ?int
    {
        return $this->manualRestarts;
    }

    /**
     * @param int|null $manualRestarts
     *
     * @return $this
     */
    public function setManualRestarts(?int $manualRestarts): self
    {
        $this->manualRestarts = $manualRestarts;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
     * @return $this
     */
    public function setMisses(?int $misses): self
    {
        $this->misses = $misses;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     *
     * @return $this
     */
    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }
}
