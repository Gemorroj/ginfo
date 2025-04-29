<?php

namespace Ginfo\Info\Database;

// https://postgrespro.ru/docs/postgresql/17/monitoring-stats#MONITORING-PG-STAT-ALL-INDEXES-VIEW
final readonly class PostgresPgStatAllIndexes
{
    public function __construct(
        private int $relid,
        private int $indexrelid,
        private string $schemaname,
        private string $relname,
        private string $indexrelname,
        private int|float $idxScan,
        private ?\DateTimeImmutable $lastIdxScan,
        private int|float $idxTupRead,
        private int|float $idxTupFetch,
    ) {
    }

    public function getRelid(): int
    {
        return $this->relid;
    }

    public function getIndexrelid(): int
    {
        return $this->indexrelid;
    }

    public function getSchemaname(): string
    {
        return $this->schemaname;
    }

    public function getRelname(): string
    {
        return $this->relname;
    }

    public function getIndexrelname(): string
    {
        return $this->indexrelname;
    }

    public function getIdxScan(): int|float
    {
        return $this->idxScan;
    }

    public function getLastIdxScan(): ?\DateTimeImmutable
    {
        return $this->lastIdxScan;
    }

    public function getIdxTupRead(): int|float
    {
        return $this->idxTupRead;
    }

    public function getIdxTupFetch(): int|float
    {
        return $this->idxTupFetch;
    }
}
