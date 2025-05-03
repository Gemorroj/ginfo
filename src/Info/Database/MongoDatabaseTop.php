<?php

namespace Ginfo\Info\Database;

final readonly class MongoDatabaseTop
{
    public function __construct(
        private int|float $totalTime,
        private int|float $totalCount,
        private int|float $readLockTime,
        private int|float $readLockCount,
        private int|float $writeLockTime,
        private int|float $writeLockCount,
        private int|float $queriesTime,
        private int|float $queriesCount,
        private int|float $getmoreTime,
        private int|float $getmoreCount,
        private int|float $insertTime,
        private int|float $insertCount,
        private int|float $updateTime,
        private int|float $updateCount,
        private int|float $removeTime,
        private int|float $removeCount,
        private int|float $commandsTime,
        private int|float $commandsCount,
    ) {
    }

    public function getTotalTime(): float|int
    {
        return $this->totalTime;
    }

    public function getTotalCount(): float|int
    {
        return $this->totalCount;
    }

    public function getReadLockTime(): float|int
    {
        return $this->readLockTime;
    }

    public function getReadLockCount(): float|int
    {
        return $this->readLockCount;
    }

    public function getWriteLockTime(): float|int
    {
        return $this->writeLockTime;
    }

    public function getWriteLockCount(): float|int
    {
        return $this->writeLockCount;
    }

    public function getQueriesTime(): float|int
    {
        return $this->queriesTime;
    }

    public function getQueriesCount(): float|int
    {
        return $this->queriesCount;
    }

    public function getGetmoreTime(): float|int
    {
        return $this->getmoreTime;
    }

    public function getGetmoreCount(): float|int
    {
        return $this->getmoreCount;
    }

    public function getInsertTime(): float|int
    {
        return $this->insertTime;
    }

    public function getInsertCount(): float|int
    {
        return $this->insertCount;
    }

    public function getUpdateTime(): float|int
    {
        return $this->updateTime;
    }

    public function getUpdateCount(): float|int
    {
        return $this->updateCount;
    }

    public function getRemoveTime(): float|int
    {
        return $this->removeTime;
    }

    public function getRemoveCount(): float|int
    {
        return $this->removeCount;
    }

    public function getCommandsTime(): float|int
    {
        return $this->commandsTime;
    }

    public function getCommandsCount(): float|int
    {
        return $this->commandsCount;
    }
}
