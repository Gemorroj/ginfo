<?php

namespace Ginfo\Info;

final readonly class Battery implements InfoInterface
{
    public function __construct(
        private string $model,
        private string $vendor,
        private string $status,
        private int $percentage,
        private int $voltageNow,
        private ?string $technology = null,
        private ?int $energyNow = null,
        private ?int $energyFull = null,
        private ?int $chargeNow = null,
        private ?int $chargeFull = null,
    ) {
    }

    /**
     * @return int|null uAh
     */
    public function getChargeFull(): ?int
    {
        return $this->chargeFull;
    }

    /**
     * @return int|null uAh
     */
    public function getChargeNow(): ?int
    {
        return $this->chargeNow;
    }

    /**
     * @return int|null uWh
     */
    public function getEnergyFull(): ?int
    {
        return $this->energyFull;
    }

    /**
     * @return int|null uWh
     */
    public function getEnergyNow(): ?int
    {
        return $this->energyNow;
    }

    /**
     * @return int uV
     */
    public function getVoltageNow(): int
    {
        return $this->voltageNow;
    }

    public function getPercentage(): int
    {
        return $this->percentage;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTechnology(): ?string
    {
        return $this->technology;
    }
}
