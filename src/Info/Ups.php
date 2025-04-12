<?php

namespace Ginfo\Info;

final readonly class Ups
{
    public function __construct(
        private string $name,
        private string $model,
        private float $batteryVolts,
        private float $batteryCharge,
        private int $timeLeft,
        private float $currentLoad,
        private string $status
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return float Volt
     */
    public function getBatteryVolts(): float
    {
        return $this->batteryVolts;
    }

    /**
     * @return float Percent
     */
    public function getBatteryCharge(): float
    {
        return $this->batteryCharge;
    }

    /**
     * @return int Seconds
     */
    public function getTimeLeft(): int
    {
        return $this->timeLeft;
    }

    /**
     * @return float Percent
     */
    public function getCurrentLoad(): float
    {
        return $this->currentLoad;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
