<?php

namespace Ginfo\Info\Php;

class Opcache
{
/*
Array (
    [opcache_enabled] => 1
    [cache_full] =>
    [restart_pending] =>
    [restart_in_progress] =>
    [memory_usage] => Array (
        [used_memory] => 18734928
        [free_memory] => 115482800
        [wasted_memory] => 0
        [current_wasted_percentage] => 0
    )
    [interned_strings_usage] => Array (
        [buffer_size] => 8388608
        [used_memory] => 237000
        [free_memory] => 8151608
        [number_of_strings] => 5547
    )
    [opcache_statistics] => Array (
        [num_cached_scripts] => 0
        [num_cached_keys] => 0
        [max_cached_keys] => 16229
        [hits] => 0
        [start_time] => 1523342080
        [last_restart_time] => 0
        [oom_restarts] => 0
        [hash_restarts] => 0
        [manual_restarts] => 0
        [misses] => 1
        [blacklist_misses] => 0
        [blacklist_miss_ratio] => 0
        [opcache_hit_rate] => 0
    )
)
 */

    /** @var bool */
    private $enabled;
    /** @var bool */
    private $configEnable;
    /** @var bool */
    private $configEnableCli;
    /** @var int */
    private $usedMemory;
    /** @var int */
    private $freeMemory;
    /** @var int */
    private $cachedScripts;
    /** @var int */
    private $hits;
    /** @var int */
    private $misses;

    /**
     * @return bool
     */
    public function isEnabled(): bool
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
     * @return bool
     */
    public function isConfigEnable(): bool
    {
        return $this->configEnable;
    }

    /**
     * @param bool $configEnable
     * @return $this
     */
    public function setConfigEnable(bool $configEnable): self
    {
        $this->configEnable = $configEnable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConfigEnableCli(): bool
    {
        return $this->configEnableCli;
    }

    /**
     * @param bool $configEnableCli
     * @return $this
     */
    public function setConfigEnableCli(bool $configEnableCli): self
    {
        $this->configEnableCli = $configEnableCli;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsedMemory(): int
    {
        return $this->usedMemory;
    }

    /**
     * @param int $usedMemory
     * @return $this
     */
    public function setUsedMemory(int $usedMemory): self
    {
        $this->usedMemory = $usedMemory;
        return $this;
    }

    /**
     * @return int
     */
    public function getFreeMemory(): int
    {
        return $this->freeMemory;
    }

    /**
     * @param int $freeMemory
     * @return $this
     */
    public function setFreeMemory(int $freeMemory): self
    {
        $this->freeMemory = $freeMemory;
        return $this;
    }

    /**
     * @return int
     */
    public function getCachedScripts(): int
    {
        return $this->cachedScripts;
    }

    /**
     * @param int $cachedScripts
     * @return $this
     */
    public function setCachedScripts(int $cachedScripts): self
    {
        $this->cachedScripts = $cachedScripts;
        return $this;
    }

    /**
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return $this
     */
    public function setHits(int $hits): self
    {
        $this->hits = $hits;
        return $this;
    }

    /**
     * @return int
     */
    public function getMisses(): int
    {
        return $this->misses;
    }

    /**
     * @param int $misses
     * @return $this
     */
    public function setMisses(int $misses): self
    {
        $this->misses = $misses;
        return $this;
    }
}
