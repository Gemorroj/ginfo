<?php

namespace Ginfo\Info\Database;

// https://postgrespro.ru/docs/postgresql/17/monitoring-stats#MONITORING-PG-STAT-ALL-TABLES-VIEW
final readonly class PostgresPgStatAllTables
{
    public function __construct(
        private int $relid,
        private string $schemaname,
        private string $relname,
        private int|float $seqScan,
        private ?\DateTimeImmutable $lastSeqScan,
        private int|float $seqTupRead,
        private int|float $idxScan,
        private ?\DateTimeImmutable $lastIdxScan,
        private int|float $idxTupFetch,
        private int|float $nTupIns,
        private int|float $nTupUpd,
        private int|float $nTupDel,
        private int|float $nTupHotUpd,
        private int|float $nTupNewpageUpd,
        private int|float $nLiveTup,
        private int|float $nDeadTup,
        private int|float $nModSinceAnalyze,
        private int|float $nInsSinceVacuum,
        private ?\DateTimeImmutable $lastVacuum,
        private ?\DateTimeImmutable $lastAutovacuum,
        private ?\DateTimeImmutable $lastAnalyze,
        private ?\DateTimeImmutable $lastAutoanalyze,
        private int|float $vacuumCount,
        private int|float $autovacuumCount,
        private int|float $analyzeCount,
        private int|float $autoanalyzeCount,
    ) {
    }

    public function getRelid(): int
    {
        return $this->relid;
    }

    public function getSchemaname(): string
    {
        return $this->schemaname;
    }

    public function getRelname(): string
    {
        return $this->relname;
    }

    public function getSeqScan(): float|int
    {
        return $this->seqScan;
    }

    public function getLastSeqScan(): ?\DateTimeImmutable
    {
        return $this->lastSeqScan;
    }

    public function getSeqTupRead(): float|int
    {
        return $this->seqTupRead;
    }

    public function getIdxScan(): float|int
    {
        return $this->idxScan;
    }

    public function getLastIdxScan(): ?\DateTimeImmutable
    {
        return $this->lastIdxScan;
    }

    public function getIdxTupFetch(): float|int
    {
        return $this->idxTupFetch;
    }

    public function getNTupIns(): float|int
    {
        return $this->nTupIns;
    }

    public function getNTupUpd(): float|int
    {
        return $this->nTupUpd;
    }

    public function getNTupDel(): float|int
    {
        return $this->nTupDel;
    }

    public function getNTupHotUpd(): float|int
    {
        return $this->nTupHotUpd;
    }

    public function getNTupNewpageUpd(): float|int
    {
        return $this->nTupNewpageUpd;
    }

    public function getNLiveTup(): float|int
    {
        return $this->nLiveTup;
    }

    public function getNDeadTup(): float|int
    {
        return $this->nDeadTup;
    }

    public function getNModSinceAnalyze(): float|int
    {
        return $this->nModSinceAnalyze;
    }

    public function getNInsSinceVacuum(): float|int
    {
        return $this->nInsSinceVacuum;
    }

    public function getLastVacuum(): ?\DateTimeImmutable
    {
        return $this->lastVacuum;
    }

    public function getLastAutovacuum(): ?\DateTimeImmutable
    {
        return $this->lastAutovacuum;
    }

    public function getLastAnalyze(): ?\DateTimeImmutable
    {
        return $this->lastAnalyze;
    }

    public function getLastAutoanalyze(): ?\DateTimeImmutable
    {
        return $this->lastAutoanalyze;
    }

    public function getVacuumCount(): float|int
    {
        return $this->vacuumCount;
    }

    public function getAutovacuumCount(): float|int
    {
        return $this->autovacuumCount;
    }

    public function getAnalyzeCount(): float|int
    {
        return $this->analyzeCount;
    }

    public function getAutoanalyzeCount(): float|int
    {
        return $this->autoanalyzeCount;
    }
}
