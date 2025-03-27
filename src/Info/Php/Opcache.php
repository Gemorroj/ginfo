<?php

namespace Ginfo\Info\Php;

final class Opcache
{
    private bool $enabled;
    private ?bool $configEnable = null;
    private ?bool $configEnableCli = null;
    private ?int $usedMemory = null;
    private ?int $freeMemory = null;
    private ?int $cachedScripts = null;
    private ?int $hits = null;
    private ?int $misses = null;
    private ?string $version = null;
    private ?int $internedStringsUsedMemory = null;
    private ?int $internedStringsFreeMemory = null;
    private ?int $cachedInternedStrings = null;
    /** number of restarts because of out of memory */
    private ?int $oomRestarts = null;
    /** number of restarts because of hash overflow */
    private ?int $hashRestarts = null;
    /** number of restarts scheduled by opcache_reset() */
    private ?int $manualRestarts = null;

    public function getInternedStringsUsedMemory(): ?int
    {
        return $this->internedStringsUsedMemory;
    }

    public function setInternedStringsUsedMemory(?int $internedStringsUsedMemory): self
    {
        $this->internedStringsUsedMemory = $internedStringsUsedMemory;

        return $this;
    }

    public function getInternedStringsFreeMemory(): ?int
    {
        return $this->internedStringsFreeMemory;
    }

    public function setInternedStringsFreeMemory(?int $internedStringsFreeMemory): self
    {
        $this->internedStringsFreeMemory = $internedStringsFreeMemory;

        return $this;
    }

    public function getCachedInternedStrings(): ?int
    {
        return $this->cachedInternedStrings;
    }

    public function setCachedInternedStrings(?int $cachedInternedStrings): self
    {
        $this->cachedInternedStrings = $cachedInternedStrings;

        return $this;
    }

    public function getOomRestarts(): ?int
    {
        return $this->oomRestarts;
    }

    public function setOomRestarts(?int $oomRestarts): self
    {
        $this->oomRestarts = $oomRestarts;

        return $this;
    }

    public function getHashRestarts(): ?int
    {
        return $this->hashRestarts;
    }

    public function setHashRestarts(?int $hashRestarts): self
    {
        $this->hashRestarts = $hashRestarts;

        return $this;
    }

    public function getManualRestarts(): ?int
    {
        return $this->manualRestarts;
    }

    public function setManualRestarts(?int $manualRestarts): self
    {
        $this->manualRestarts = $manualRestarts;

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

    public function getCachedScripts(): ?int
    {
        return $this->cachedScripts;
    }

    public function setCachedScripts(?int $cachedScripts): self
    {
        $this->cachedScripts = $cachedScripts;

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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }
}
