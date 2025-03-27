<?php

namespace Ginfo\Info;

use Ginfo\Info\Network\Stats;

final class Network
{
    private string $name;
    private ?float $speed = null;
    private ?string $type = null;
    private ?string $state = null;
    private ?Stats $statsReceived = null;
    private ?Stats $statsSent = null;

    public function getStatsReceived(): ?Stats
    {
        return $this->statsReceived;
    }

    public function setStatsReceived(?Stats $statsReceived): self
    {
        $this->statsReceived = $statsReceived;

        return $this;
    }

    public function getStatsSent(): ?Stats
    {
        return $this->statsSent;
    }

    public function setStatsSent(?Stats $statsSent): self
    {
        $this->statsSent = $statsSent;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float|null bit/s
     */
    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    /**
     * @param float|null $speed bit/s
     */
    public function setSpeed(?float $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
