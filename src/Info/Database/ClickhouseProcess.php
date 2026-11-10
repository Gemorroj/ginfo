<?php

namespace Ginfo\Info\Database;

final readonly class ClickhouseProcess
{
    public function __construct(
        private string $queryId,
        private string $user,
        private float $elapsed,
        private int $readRows,
        private int $readBytes,
        private int $memoryUsage,
        private string $query,
    ) {
    }

    public function getQueryId(): string
    {
        return $this->queryId;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return float seconds
     */
    public function getElapsed(): float
    {
        return $this->elapsed;
    }

    public function getReadRows(): int
    {
        return $this->readRows;
    }

    public function getReadBytes(): int
    {
        return $this->readBytes;
    }

    public function getMemoryUsage(): int
    {
        return $this->memoryUsage;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
