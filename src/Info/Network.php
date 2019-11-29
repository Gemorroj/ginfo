<?php

namespace Ginfo\Info;

use Ginfo\Info\Network\Stats;

class Network
{
    /** @var string */
    private $name;
    /** @var float|null */
    private $speed;
    /** @var string|null */
    private $type;
    /** @var string|null */
    private $state;
    /** @var Stats|null */
    private $statsReceived;
    /** @var Stats|null */
    private $statsSent;

    public function getStatsReceived(): ?Stats
    {
        return $this->statsReceived;
    }

    /**
     * @return $this
     */
    public function setStatsReceived(?Stats $statsReceived): self
    {
        $this->statsReceived = $statsReceived;

        return $this;
    }

    public function getStatsSent(): ?Stats
    {
        return $this->statsSent;
    }

    /**
     * @return $this
     */
    public function setStatsSent(?Stats $statsSent): self
    {
        $this->statsSent = $statsSent;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
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
     *
     * @return $this
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

    /**
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
