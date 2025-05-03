<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Sqlite implements InfoInterface
{
    public function __construct(
        private string $sqliteVersion,
        private string $sqliteSourceId,
        private int|float $dbSize,
        private SqlitePragma $pragma,
    ) {
    }

    public function getSqliteVersion(): string
    {
        return $this->sqliteVersion;
    }

    public function getSqliteSourceId(): string
    {
        return $this->sqliteSourceId;
    }

    public function getDbSize(): float|int
    {
        return $this->dbSize;
    }

    public function getPragma(): SqlitePragma
    {
        return $this->pragma;
    }
}
