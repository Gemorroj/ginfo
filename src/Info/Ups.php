<?php

namespace Ginfo\Info;

class Ups
{
    /** @var string */
    private $name;
    /** @var string */
    private $model;
    /** @var float */
    private $batteryVolts;
    /** @var float */
    private $batteryCharge;
    /** @var int */
    private $timeLeft;
    /** @var float */
    private $currentLoad;
    /** @var string */
    private $status;

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

    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return $this
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return float Volt
     */
    public function getBatteryVolts(): float
    {
        return $this->batteryVolts;
    }

    /**
     * @return $this
     */
    public function setBatteryVolts(float $batteryVolts): self
    {
        $this->batteryVolts = $batteryVolts;

        return $this;
    }

    /**
     * @return float Percent
     */
    public function getBatteryCharge(): float
    {
        return $this->batteryCharge;
    }

    /**
     * @return $this
     */
    public function setBatteryCharge(float $batteryCharge): self
    {
        $this->batteryCharge = $batteryCharge;

        return $this;
    }

    /**
     * @return int Seconds
     */
    public function getTimeLeft(): int
    {
        return $this->timeLeft;
    }

    /**
     * @return $this
     */
    public function setTimeLeft(int $timeLeft): self
    {
        $this->timeLeft = $timeLeft;

        return $this;
    }

    /**
     * @return float Percent
     */
    public function getCurrentLoad(): float
    {
        return $this->currentLoad;
    }

    /**
     * @return $this
     */
    public function setCurrentLoad(float $currentLoad): self
    {
        $this->currentLoad = $currentLoad;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
