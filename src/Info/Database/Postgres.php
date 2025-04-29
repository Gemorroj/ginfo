<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Postgres implements InfoInterface
{
    /**
     * @param PostgresPgStatActivity[]   $pgStatActivity
     * @param PostgresPgStatDatabase[]   $pgStatDatabase
     * @param PostgresPgStatAllTables[]  $pgStatAllTables
     * @param PostgresPgStatAllIndexes[] $pgStatAllIndexes
     * @param PostgresPgStatStatements[] $pgStatStatements
     */
    public function __construct(
        private string $version,
        private array $pgStatActivity,
        private array $pgStatDatabase,
        private array $pgStatAllTables,
        private array $pgStatAllIndexes,
        private array $pgStatStatements,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return PostgresPgStatActivity[]
     */
    public function getPgStatActivity(): array
    {
        return $this->pgStatActivity;
    }

    /**
     * @return PostgresPgStatDatabase[]
     */
    public function getPgStatDatabase(): array
    {
        return $this->pgStatDatabase;
    }

    /**
     * @return PostgresPgStatAllTables[]
     */
    public function getPgStatAllTables(): array
    {
        return $this->pgStatAllTables;
    }

    /**
     * @return PostgresPgStatAllIndexes[]
     */
    public function getPgStatAllIndexes(): array
    {
        return $this->pgStatAllIndexes;
    }

    /**
     * @return PostgresPgStatStatements[]
     */
    public function getPgStatStatements(): array
    {
        return $this->pgStatStatements;
    }
}
