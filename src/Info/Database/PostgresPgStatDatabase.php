<?php

namespace Ginfo\Info\Database;

// https://postgrespro.ru/docs/postgresql/17/monitoring-stats#MONITORING-PG-STAT-DATABASE-VIEW
final readonly class PostgresPgStatDatabase
{
    public function __construct(
        private int $datid,
        private ?string $datname,
        private int $numbackends,
        private int|float $xactCommit,
        private int|float $xactRollback,
        private int|float $blksRead,
        private int|float $blksHit,
        private int|float $tupReturned,
        private int|float $tupFetched,
        private int|float $tupInserted,
        private int|float $tupUpdated,
        private int|float $tupDeleted,
        private int|float $conflicts,
        private int|float $tempFiles,
        private int|float $tempBytes,
        private int|float $deadlocks,
        private int|float|null $checksumFailures,
        private ?\DateTimeImmutable $checksumLastFailure,
        private float $blkReadTime,
        private float $blkWriteTime,
        private float $sessionTime,
        private float $activeTime,
        private float $idleInTransactionTime,
        private int|float $sessions,
        private int|float $sessionsAbandoned,
        private int|float $sessionsFatal,
        private int|float $sessionsKilled,
        private ?\DateTimeImmutable $statsReset,
    ) {
    }

    public function getDatid(): int
    {
        return $this->datid;
    }

    public function getDatname(): ?string
    {
        return $this->datname;
    }

    public function getNumbackends(): int
    {
        return $this->numbackends;
    }

    public function getXactCommit(): float|int
    {
        return $this->xactCommit;
    }

    public function getXactRollback(): float|int
    {
        return $this->xactRollback;
    }

    public function getBlksRead(): float|int
    {
        return $this->blksRead;
    }

    public function getBlksHit(): float|int
    {
        return $this->blksHit;
    }

    public function getTupReturned(): float|int
    {
        return $this->tupReturned;
    }

    public function getTupFetched(): float|int
    {
        return $this->tupFetched;
    }

    public function getTupInserted(): float|int
    {
        return $this->tupInserted;
    }

    public function getTupUpdated(): float|int
    {
        return $this->tupUpdated;
    }

    public function getTupDeleted(): float|int
    {
        return $this->tupDeleted;
    }

    public function getConflicts(): float|int
    {
        return $this->conflicts;
    }

    public function getTempFiles(): float|int
    {
        return $this->tempFiles;
    }

    public function getTempBytes(): float|int
    {
        return $this->tempBytes;
    }

    public function getDeadlocks(): float|int
    {
        return $this->deadlocks;
    }

    public function getChecksumFailures(): float|int|null
    {
        return $this->checksumFailures;
    }

    public function getChecksumLastFailure(): ?\DateTimeImmutable
    {
        return $this->checksumLastFailure;
    }

    public function getBlkReadTime(): float
    {
        return $this->blkReadTime;
    }

    public function getBlkWriteTime(): float
    {
        return $this->blkWriteTime;
    }

    public function getSessionTime(): float
    {
        return $this->sessionTime;
    }

    public function getActiveTime(): float
    {
        return $this->activeTime;
    }

    public function getIdleInTransactionTime(): float
    {
        return $this->idleInTransactionTime;
    }

    public function getSessions(): float|int
    {
        return $this->sessions;
    }

    public function getSessionsAbandoned(): float|int
    {
        return $this->sessionsAbandoned;
    }

    public function getSessionsFatal(): float|int
    {
        return $this->sessionsFatal;
    }

    public function getSessionsKilled(): float|int
    {
        return $this->sessionsKilled;
    }

    public function getStatsReset(): ?\DateTimeImmutable
    {
        return $this->statsReset;
    }
}
