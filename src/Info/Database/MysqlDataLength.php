<?php

namespace Ginfo\Info\Database;

final readonly class MysqlDataLength
{
    public function __construct(
        private string $tableSchema,
        private string $tableName,
        private int|float $dataLength,
        private int|float $indexLength,
    ) {
    }

    public function getTableSchema(): string
    {
        return $this->tableSchema;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getDataLength(): float|int
    {
        return $this->dataLength;
    }

    public function getIndexLength(): float|int
    {
        return $this->indexLength;
    }
}
