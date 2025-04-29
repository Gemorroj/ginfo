<?php

namespace Ginfo\Info\Database;

// https://postgrespro.ru/docs/postgresql/17/pgstatstatements#PGSTATSTATEMENTS-PG-STAT-STATEMENTS
final readonly class PostgresPgStatStatements
{
    public function __construct(
        private int $userid,
        private int $dbid,
        private bool $toplevel,
        private int|float $queryid,
        private string $query,
        private int|float $plans,
        private float $totalPlanTime,
        private float $minPlanTime,
        private float $maxPlanTime,
        private float $meanPlanTime,
        private float $stddevPlanTime,
        private int|float $calls,
        private float $totalExecTime,
        private float $minExecTime,
        private float $maxExecTime,
        private float $meanExecTime,
        private float $stddevExecTime,
        private int|float $rows,
        private int|float $sharedBlksHit,
        private int|float $sharedBlksRead,
        private int|float $sharedBlksDirtied,
        private int|float $sharedBlksWritten,
        private int|float $localBlksHit,
        private int|float $localBlksRead,
        private int|float $localBlksDirtied,
        private int|float $localBlksWritten,
        private int|float $tempBlksRead,
        private int|float $tempBlksWritten,
        private float $sharedBlkReadTime,
        private float $sharedBlkWriteTime,
        private float $localBlkReadTime,
        private float $localBlkWriteTime,
        private float $tempBlkReadTime,
        private float $tempBlkWriteTime,
        private int|float $walRecords,
        private int|float $walFpi,
        private float $walBytes,
        private int|float $jitFunctions,
        private float $jitGenerationTime,
        private int|float $jitInliningCount,
        private float $jitInliningTime,
        private int|float $jitOptimizationCount,
        private float $jitOptimizationTime,
        private int|float $jitEmissionCount,
        private float $jitEmissionTime,
        private int|float $jitDeformCount,
        private float $jitDeformTime,
        private \DateTimeImmutable $statsSince,
        private \DateTimeImmutable $minmaxStatsSince,
    ) {
    }

    public function getUserid(): int
    {
        return $this->userid;
    }

    public function getDbid(): int
    {
        return $this->dbid;
    }

    public function isToplevel(): bool
    {
        return $this->toplevel;
    }

    public function getQueryid(): float|int
    {
        return $this->queryid;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getPlans(): float|int
    {
        return $this->plans;
    }

    public function getTotalPlanTime(): float
    {
        return $this->totalPlanTime;
    }

    public function getMinPlanTime(): float
    {
        return $this->minPlanTime;
    }

    public function getMaxPlanTime(): float
    {
        return $this->maxPlanTime;
    }

    public function getMeanPlanTime(): float
    {
        return $this->meanPlanTime;
    }

    public function getStddevPlanTime(): float
    {
        return $this->stddevPlanTime;
    }

    public function getCalls(): float|int
    {
        return $this->calls;
    }

    public function getTotalExecTime(): float
    {
        return $this->totalExecTime;
    }

    public function getMinExecTime(): float
    {
        return $this->minExecTime;
    }

    public function getMaxExecTime(): float
    {
        return $this->maxExecTime;
    }

    public function getMeanExecTime(): float
    {
        return $this->meanExecTime;
    }

    public function getStddevExecTime(): float
    {
        return $this->stddevExecTime;
    }

    public function getRows(): float|int
    {
        return $this->rows;
    }

    public function getSharedBlksHit(): float|int
    {
        return $this->sharedBlksHit;
    }

    public function getSharedBlksRead(): float|int
    {
        return $this->sharedBlksRead;
    }

    public function getSharedBlksDirtied(): float|int
    {
        return $this->sharedBlksDirtied;
    }

    public function getSharedBlksWritten(): float|int
    {
        return $this->sharedBlksWritten;
    }

    public function getLocalBlksHit(): float|int
    {
        return $this->localBlksHit;
    }

    public function getLocalBlksRead(): float|int
    {
        return $this->localBlksRead;
    }

    public function getLocalBlksDirtied(): float|int
    {
        return $this->localBlksDirtied;
    }

    public function getLocalBlksWritten(): float|int
    {
        return $this->localBlksWritten;
    }

    public function getTempBlksRead(): float|int
    {
        return $this->tempBlksRead;
    }

    public function getTempBlksWritten(): float|int
    {
        return $this->tempBlksWritten;
    }

    public function getSharedBlkReadTime(): float
    {
        return $this->sharedBlkReadTime;
    }

    public function getSharedBlkWriteTime(): float
    {
        return $this->sharedBlkWriteTime;
    }

    public function getLocalBlkReadTime(): float
    {
        return $this->localBlkReadTime;
    }

    public function getLocalBlkWriteTime(): float
    {
        return $this->localBlkWriteTime;
    }

    public function getTempBlkReadTime(): float
    {
        return $this->tempBlkReadTime;
    }

    public function getTempBlkWriteTime(): float
    {
        return $this->tempBlkWriteTime;
    }

    public function getWalRecords(): float|int
    {
        return $this->walRecords;
    }

    public function getWalFpi(): float|int
    {
        return $this->walFpi;
    }

    public function getWalBytes(): float
    {
        return $this->walBytes;
    }

    public function getJitFunctions(): float|int
    {
        return $this->jitFunctions;
    }

    public function getJitGenerationTime(): float
    {
        return $this->jitGenerationTime;
    }

    public function getJitInliningCount(): float|int
    {
        return $this->jitInliningCount;
    }

    public function getJitInliningTime(): float
    {
        return $this->jitInliningTime;
    }

    public function getJitOptimizationCount(): float|int
    {
        return $this->jitOptimizationCount;
    }

    public function getJitOptimizationTime(): float
    {
        return $this->jitOptimizationTime;
    }

    public function getJitEmissionCount(): float|int
    {
        return $this->jitEmissionCount;
    }

    public function getJitEmissionTime(): float
    {
        return $this->jitEmissionTime;
    }

    public function getJitDeformCount(): float|int
    {
        return $this->jitDeformCount;
    }

    public function getJitDeformTime(): float
    {
        return $this->jitDeformTime;
    }

    public function getStatsSince(): \DateTimeImmutable
    {
        return $this->statsSince;
    }

    public function getMinmaxStatsSince(): \DateTimeImmutable
    {
        return $this->minmaxStatsSince;
    }
}
