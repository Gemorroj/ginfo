<?php

namespace Ginfo\Info\Database;

final readonly class MongoServerStatusConnections
{
    public function __construct(
        private int|float $current,
        private int|float $available,
        private int|float $totalCreated,
        private int|float $rejected,
        private int|float $active,
        private int|float $threaded,
        private int|float $exhaustIsMaster,
        private int|float $exhaustHello,
        private int|float $awaitingTopologyChanges,
        private int|float $loadBalanced,
    ) {
    }

    public function getCurrent(): float|int
    {
        return $this->current;
    }

    public function getAvailable(): float|int
    {
        return $this->available;
    }

    public function getTotalCreated(): float|int
    {
        return $this->totalCreated;
    }

    public function getRejected(): float|int
    {
        return $this->rejected;
    }

    public function getActive(): float|int
    {
        return $this->active;
    }

    public function getThreaded(): float|int
    {
        return $this->threaded;
    }

    public function getExhaustIsMaster(): float|int
    {
        return $this->exhaustIsMaster;
    }

    public function getExhaustHello(): float|int
    {
        return $this->exhaustHello;
    }

    public function getAwaitingTopologyChanges(): float|int
    {
        return $this->awaitingTopologyChanges;
    }

    public function getLoadBalanced(): float|int
    {
        return $this->loadBalanced;
    }
}
