<?php

namespace Ginfo\Info\Database;

final readonly class MysqlPerformance95thPercentile
{
    /**
     * @param array{value: float, unit: string} $totalLatency
     * @param array{value: float, unit: string} $maxLatency
     * @param array{value: float, unit: string} $avgLatency
     */
    public function __construct(
        private string $query,
        private string $db,
        private bool $fullScan,
        private float|int $execCount,
        private float|int $errCount,
        private float|int $warnCount,
        private array $totalLatency,
        private array $maxLatency,
        private array $avgLatency,
        private float|int $rowsSent,
        private float|int $rowsSentAvg,
        private float|int $rowsExamined,
        private float|int $rowsExaminedAvg,
        private \DateTimeImmutable $firstSeen,
        private \DateTimeImmutable $lastSeen,
        private string $digest,
    ) {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getDb(): string
    {
        return $this->db;
    }

    public function isFullScan(): bool
    {
        return $this->fullScan;
    }

    public function getExecCount(): float|int
    {
        return $this->execCount;
    }

    public function getErrCount(): float|int
    {
        return $this->errCount;
    }

    public function getWarnCount(): float|int
    {
        return $this->warnCount;
    }

    /**
     * @return array{value: float, unit: string}
     */
    public function getTotalLatency(): array
    {
        return $this->totalLatency;
    }

    /**
     * @return array{value: float, unit: string}
     */
    public function getMaxLatency(): array
    {
        return $this->maxLatency;
    }

    /**
     * @return array{value: float, unit: string}
     */
    public function getAvgLatency(): array
    {
        return $this->avgLatency;
    }

    public function getRowsSent(): float|int
    {
        return $this->rowsSent;
    }

    public function getRowsSentAvg(): float|int
    {
        return $this->rowsSentAvg;
    }

    public function getRowsExamined(): float|int
    {
        return $this->rowsExamined;
    }

    public function getRowsExaminedAvg(): float|int
    {
        return $this->rowsExaminedAvg;
    }

    public function getFirstSeen(): \DateTimeImmutable
    {
        return $this->firstSeen;
    }

    public function getLastSeen(): \DateTimeImmutable
    {
        return $this->lastSeen;
    }

    public function getDigest(): string
    {
        return $this->digest;
    }
}
