<?php

namespace Ginfo\Info;

final class Battery
{
    private ?int $chargeFull = null;
    private ?int $chargeNow = null;
    private ?int $energyFull = null;
    private ?int $energyNow = null;
    private int $voltageNow;
    private int $percentage;
    private string $vendor;
    private string $model;
    private string $status;
    private string $technology;

    /**
     * @return int|null uAh
     */
    public function getChargeFull(): ?int
    {
        return $this->chargeFull;
    }

    /**
     * @param int|null $chargeFull uAh
     */
    public function setChargeFull(?int $chargeFull): self
    {
        $this->chargeFull = $chargeFull;

        return $this;
    }

    /**
     * @return int|null uAh
     */
    public function getChargeNow(): ?int
    {
        return $this->chargeNow;
    }

    /**
     * @param int|null $chargeNow uAh
     */
    public function setChargeNow(?int $chargeNow): self
    {
        $this->chargeNow = $chargeNow;

        return $this;
    }

    /**
     * @return int|null uWh
     */
    public function getEnergyFull(): ?int
    {
        return $this->energyFull;
    }

    /**
     * @param int|null $energyFull uWh
     */
    public function setEnergyFull(?int $energyFull): self
    {
        $this->energyFull = $energyFull;

        return $this;
    }

    /**
     * @return int|null uWh
     */
    public function getEnergyNow(): ?int
    {
        return $this->energyNow;
    }

    /**
     * @param int|null $energyNow uWh
     */
    public function setEnergyNow(?int $energyNow): self
    {
        $this->energyNow = $energyNow;

        return $this;
    }

    /**
     * @return int uV
     */
    public function getVoltageNow(): int
    {
        return $this->voltageNow;
    }

    /**
     * @param int $voltageNow uV
     */
    public function setVoltageNow(int $voltageNow): self
    {
        $this->voltageNow = $voltageNow;

        return $this;
    }

    public function getPercentage(): int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTechnology(): string
    {
        return $this->technology;
    }

    public function setTechnology(string $technology): self
    {
        $this->technology = $technology;

        return $this;
    }
}
