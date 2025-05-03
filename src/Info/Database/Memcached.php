<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Memcached implements InfoInterface
{
    /**
     * @param array<string, array<string, string|int|float>> $stats
     * @param array<string, array<string, string|int|float>> $statsSettings
     * @param array<string, array<string, string|int|float>> $statsConns
     */
    public function __construct(
        private array $stats,
        private array $statsSettings,
        private array $statsConns,
    ) {
    }

    /**
     * @return array<string, array<string, string|int|float>>
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * @return array<string, array<string, string|int|float>>
     */
    public function getStatsSettings(): array
    {
        return $this->statsSettings;
    }

    /**
     * @return array<string, array<string, string|int|float>>
     */
    public function getStatsConns(): array
    {
        return $this->statsConns;
    }
}
