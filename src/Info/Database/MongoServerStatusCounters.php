<?php

namespace Ginfo\Info\Database;

final readonly class MongoServerStatusCounters
{
    public function __construct(
        private int|float $insert,
        private int|float $query,
        private int|float $update,
        private int|float $delete,
        private int|float $getmore,
        private int|float $command,
    ) {
    }

    public function getInsert(): float|int
    {
        return $this->insert;
    }

    public function getQuery(): float|int
    {
        return $this->query;
    }

    public function getUpdate(): float|int
    {
        return $this->update;
    }

    public function getDelete(): float|int
    {
        return $this->delete;
    }

    public function getGetmore(): float|int
    {
        return $this->getmore;
    }

    public function getCommand(): float|int
    {
        return $this->command;
    }
}
