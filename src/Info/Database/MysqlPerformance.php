<?php

namespace Ginfo\Info\Database;

final readonly class MysqlPerformance
{
    public function __construct(
        private string $schemaName,
        private int|float $count,
        private float $avgMicrosec,
    ) {
    }

    public function getSchemaName(): string
    {
        return $this->schemaName;
    }

    public function getCount(): float|int
    {
        return $this->count;
    }

    public function getAvgMicrosec(): float
    {
        return $this->avgMicrosec;
    }
}
