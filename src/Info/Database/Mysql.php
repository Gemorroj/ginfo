<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Mysql implements InfoInterface
{
    /**
     * @param array<string, string> $globalStatus
     * @param array<string, string> $variables
     * @param MysqlPerformance[]    $performance
     * @param MysqlSummary[]        $summary
     */
    public function __construct(
        private array $globalStatus,
        private array $variables,
        private array $performance,
        private array $summary,
    ) {
    }

    public function getGlobalStatus(): array
    {
        return $this->globalStatus;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return MysqlPerformance[]
     */
    public function getPerformance(): array
    {
        return $this->performance;
    }

    /**
     * @return MysqlSummary[]
     */
    public function getSummary(): array
    {
        return $this->summary;
    }
}
