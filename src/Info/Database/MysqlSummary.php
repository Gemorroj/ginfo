<?php

namespace Ginfo\Info\Database;

final readonly class MysqlSummary
{
    public function __construct(
        private string $host,
        private string $statement,
        private float|int $total,
        private string $totalLatency,
        private string $maxLatency,
        private string $lockLatency,
        private string $cpuLatency,
        private float|int $rowsSent,
        private float|int $rowsExamined,
        private float|int $rowsAffected,
        private float|int $fullScans,
    ) {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getStatement(): string
    {
        return $this->statement;
    }

    public function getTotal(): float|int
    {
        return $this->total;
    }

    public function getTotalLatency(): string
    {
        return $this->totalLatency;
    }

    public function getMaxLatency(): string
    {
        return $this->maxLatency;
    }

    public function getLockLatency(): string
    {
        return $this->lockLatency;
    }

    public function getCpuLatency(): string
    {
        return $this->cpuLatency;
    }

    public function getRowsSent(): float|int
    {
        return $this->rowsSent;
    }

    public function getRowsExamined(): float|int
    {
        return $this->rowsExamined;
    }

    public function getRowsAffected(): float|int
    {
        return $this->rowsAffected;
    }

    public function getFullScans(): float|int
    {
        return $this->fullScans;
    }
}
