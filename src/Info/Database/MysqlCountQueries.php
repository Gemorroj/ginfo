<?php

namespace Ginfo\Info\Database;

final readonly class MysqlCountQueries
{
    public function __construct(
        private string $objectType,
        private string $objectSchema,
        private string $objectName,
        private int|float $countRead,
        private int|float $countWrite,
        private int|float $countFetch,
        private int|float $countInsert,
        private int|float $countUpdate,
        private int|float $countDelete,
    ) {
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getObjectSchema(): string
    {
        return $this->objectSchema;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function getCountRead(): float|int
    {
        return $this->countRead;
    }

    public function getCountWrite(): float|int
    {
        return $this->countWrite;
    }

    public function getCountFetch(): float|int
    {
        return $this->countFetch;
    }

    public function getCountInsert(): float|int
    {
        return $this->countInsert;
    }

    public function getCountUpdate(): float|int
    {
        return $this->countUpdate;
    }

    public function getCountDelete(): float|int
    {
        return $this->countDelete;
    }
}
