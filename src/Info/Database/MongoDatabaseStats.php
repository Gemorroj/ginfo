<?php

namespace Ginfo\Info\Database;

final readonly class MongoDatabaseStats
{
    public function __construct(
        private string $db,
        private int $collections,
        private int $views,
        private int $objects,
        private int|float $avgObjSize,
        private int|float $dataSize,
        private int|float $storageSize,
        private int $indexes,
        private int|float $indexSize,
        private int|float $totalSize,
        private int $scaleFactor,
        private int|float $fsUsedSize,
        private int|float $fsTotalSize,
        private int $ok,
    ) {
    }

    public function getDb(): string
    {
        return $this->db;
    }

    public function getCollections(): int
    {
        return $this->collections;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function getObjects(): int
    {
        return $this->objects;
    }

    public function getAvgObjSize(): float|int
    {
        return $this->avgObjSize;
    }

    public function getDataSize(): float|int
    {
        return $this->dataSize;
    }

    public function getStorageSize(): float|int
    {
        return $this->storageSize;
    }

    public function getIndexes(): int
    {
        return $this->indexes;
    }

    public function getIndexSize(): float|int
    {
        return $this->indexSize;
    }

    public function getTotalSize(): float|int
    {
        return $this->totalSize;
    }

    public function getScaleFactor(): int
    {
        return $this->scaleFactor;
    }

    public function getFsUsedSize(): float|int
    {
        return $this->fsUsedSize;
    }

    public function getFsTotalSize(): float|int
    {
        return $this->fsTotalSize;
    }

    public function getOk(): int
    {
        return $this->ok;
    }
}
