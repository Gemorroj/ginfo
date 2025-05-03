<?php

namespace Ginfo\Info\Database;

final readonly class MongoDatabase
{
    /**
     * @param array<string, MongoDatabaseTop> $top
     */
    public function __construct(
        private int|float $sizeOnDisk,
        private bool $empty,
        private MongoDatabaseStats $stats,
        private array $top,
    ) {
    }

    public function getSizeOnDisk(): float|int
    {
        return $this->sizeOnDisk;
    }

    public function isEmpty(): bool
    {
        return $this->empty;
    }

    public function getStats(): MongoDatabaseStats
    {
        return $this->stats;
    }

    /**
     * @return array<string, MongoDatabaseTop>
     */
    public function getTop(): array
    {
        return $this->top;
    }
}
