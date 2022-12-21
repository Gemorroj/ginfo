<?php

namespace Ginfo\Info;

class Ups
{
    private string $name;
    private string $model;
    private float $batteryVolts;
    private float $batteryCharge;
    private int $timeLeft;
    private float $currentLoad;
    private string $status;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

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

    public function setCurrentLoad(float $currentLoad): self
    {
        $this->currentLoad = $currentLoad;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
