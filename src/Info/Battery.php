<?php

namespace Ginfo\Info;

class Battery
{
    /** @var int|null */
    private $chargeFull;
    /** @var int|null */
    private $chargeNow;
    /** @var int|null */
    private $energyFull;
    /** @var int|null */
    private $energyNow;
    /** @var int */
    private $voltageNow;
    /** @var int */
    private $percentage;
    /** @var string */
    private $vendor;
    /** @var string */
    private $model;
    /** @var string */
    private $status;
    /** @var string */
    private $technology;

    /**
     * @return int|null uAh
     */
    public function getChargeFull(): ?int
    {
        return $this->chargeFull;
    }

    /**
     * @param int|null $chargeFull uAh
     *
     * @return $this
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
     *
     * @return $this
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
     *
     * @return $this
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
     *
     * @return $this
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
     *
     * @return $this
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

    /**
     * @return $this
     */
    public function setPercentage(int $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    /**
     * @return $this
     */
    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;

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

    public function getTechnology(): string
    {
        return $this->technology;
    }

    /**
     * @return $this
     */
    public function setTechnology(string $technology): self
    {
        $this->technology = $technology;

        return $this;
    }
}
