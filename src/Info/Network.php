<?php

namespace Ginfo\Info;

use Ginfo\Info\Network\Stats;

final readonly class Network implements InfoInterface
{
    public function __construct(
        private string $name,
        private ?float $speed = null,
        private ?string $type = null,
        private ?string $state = null,
        private ?Stats $statsReceived = null,
        private ?Stats $statsSent = null
    ) {
    }

    public function getStatsReceived(): ?Stats
    {
        return $this->statsReceived;
    }

    public function getStatsSent(): ?Stats
    {
        return $this->statsSent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float|null bit/s
     */
    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getState(): ?string
    {
        return $this->state;
    }
}
