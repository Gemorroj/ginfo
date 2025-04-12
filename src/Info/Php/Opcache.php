<?php

namespace Ginfo\Info\Php;

final readonly class Opcache
{
    public function __construct(
        private bool $enabled,
        private ?string $version = null,
        private ?bool $configEnable = null,
        private ?bool $configEnableCli = null,
        private ?int $usedMemory = null,
        private ?int $freeMemory = null,
        private ?int $cachedScripts = null,
        private ?int $hits = null,
        private ?int $misses = null,
        private ?int $internedStringsUsedMemory = null,
        private ?int $internedStringsFreeMemory = null,
        private ?int $cachedInternedStrings = null,
        /** number of restarts because of out of memory */
        private ?int $oomRestarts = null,
        /** number of restarts because of hash overflow */
        private ?int $hashRestarts = null,
        /** number of restarts scheduled by opcache_reset() */
        private ?int $manualRestarts = null
    ) {
    }

    public function getInternedStringsUsedMemory(): ?int
    {
        return $this->internedStringsUsedMemory;
    }

    public function getInternedStringsFreeMemory(): ?int
    {
        return $this->internedStringsFreeMemory;
    }

    public function getCachedInternedStrings(): ?int
    {
        return $this->cachedInternedStrings;
    }

    public function getOomRestarts(): ?int
    {
        return $this->oomRestarts;
    }

    public function getHashRestarts(): ?int
    {
        return $this->hashRestarts;
    }

    public function getManualRestarts(): ?int
    {
        return $this->manualRestarts;
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

    public function getUsedMemory(): ?int
    {
        return $this->usedMemory;
    }

    public function getFreeMemory(): ?int
    {
        return $this->freeMemory;
    }

    public function getCachedScripts(): ?int
    {
        return $this->cachedScripts;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function getMisses(): ?int
    {
        return $this->misses;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }
}
