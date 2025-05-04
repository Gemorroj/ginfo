<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class ElasticsearchStatsIndices implements InfoInterface
{
    public function __construct(
        private int $count,
        private int|float $storeSizeInBytes,
        private int|float $storeReservedInBytes,
        private int|float $fielddataMemorySizeInBytes,
        private int|float $fielddataEvictions,
        private int|float $queryCacheMemorySizeInBytes,
        private int|float $queryCacheTotalCount,
        private int|float $queryCacheHitCount,
        private int|float $queryCacheMissCount,
        private int|float $queryCacheCacheSize,
        private int|float $queryCacheCacheCount,
        private int|float $queryCacheEvictions,
        private int|float $completionSizeInBytes,
        private int|float $segmentsCount,
        private int|float $segmentsMemoryInBytes,
    ) {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getStoreSizeInBytes(): float|int
    {
        return $this->storeSizeInBytes;
    }

    public function getStoreReservedInBytes(): float|int
    {
        return $this->storeReservedInBytes;
    }

    public function getFielddataMemorySizeInBytes(): float|int
    {
        return $this->fielddataMemorySizeInBytes;
    }

    public function getFielddataEvictions(): float|int
    {
        return $this->fielddataEvictions;
    }

    public function getQueryCacheMemorySizeInBytes(): float|int
    {
        return $this->queryCacheMemorySizeInBytes;
    }

    public function getQueryCacheTotalCount(): float|int
    {
        return $this->queryCacheTotalCount;
    }

    public function getQueryCacheHitCount(): float|int
    {
        return $this->queryCacheHitCount;
    }

    public function getQueryCacheMissCount(): float|int
    {
        return $this->queryCacheMissCount;
    }

    public function getQueryCacheCacheSize(): float|int
    {
        return $this->queryCacheCacheSize;
    }

    public function getQueryCacheCacheCount(): float|int
    {
        return $this->queryCacheCacheCount;
    }

    public function getQueryCacheEvictions(): float|int
    {
        return $this->queryCacheEvictions;
    }

    public function getCompletionSizeInBytes(): float|int
    {
        return $this->completionSizeInBytes;
    }

    public function getSegmentsCount(): float|int
    {
        return $this->segmentsCount;
    }

    public function getSegmentsMemoryInBytes(): float|int
    {
        return $this->segmentsMemoryInBytes;
    }
}
