<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Mysql implements InfoInterface
{
    /**
     * @param array<string, string>            $globalStatus
     * @param array<string, string>            $globalVariables
     * @param MysqlPerformance95thPercentile[] $performance95thPercentile
     * @param MysqlCountQueries[]              $countQueries
     * @param MysqlDataLength[]                $dataLength
     */
    public function __construct(
        private array $globalStatus,
        private array $globalVariables,
        private array $performance95thPercentile,
        private array $countQueries,
        private array $dataLength,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getGlobalStatus(): array
    {
        return $this->globalStatus;
    }

    /**
     * @return array<string, string>
     */
    public function getGlobalVariables(): array
    {
        return $this->globalVariables;
    }

    /**
     * @return MysqlPerformance95thPercentile[]
     */
    public function getPerformance95thPercentile(): array
    {
        return $this->performance95thPercentile;
    }

    /**
     * @return MysqlCountQueries[]
     */
    public function getCountQueries(): array
    {
        return $this->countQueries;
    }

    /**
     * @return MysqlDataLength[]
     */
    public function getDataLength(): array
    {
        return $this->dataLength;
    }
}
