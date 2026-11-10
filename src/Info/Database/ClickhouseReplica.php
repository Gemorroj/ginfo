<?php

namespace Ginfo\Info\Database;

final readonly class ClickhouseReplica
{
    public function __construct(
        private string $database,
        private string $table,
        private string $engine,
        private bool $readonly,
        private int $absoluteDelay,
        private int $queueSize,
        private int $totalReplicas,
        private int $activeReplicas,
    ) {
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @return int seconds
     */
    public function getAbsoluteDelay(): int
    {
        return $this->absoluteDelay;
    }

    public function getQueueSize(): int
    {
        return $this->queueSize;
    }

    public function getTotalReplicas(): int
    {
        return $this->totalReplicas;
    }

    public function getActiveReplicas(): int
    {
        return $this->activeReplicas;
    }
}
