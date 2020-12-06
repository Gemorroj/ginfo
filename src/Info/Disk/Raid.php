<?php

namespace Ginfo\Info\Disk;

use Ginfo\Info\Disk\Raid\Drive;

class Raid
{
    /** @var string */
    private $device;
    /** @var string */
    private $status;
    /** @var int */
    private $level;
    /** @var Drive[] */
    private $drives;
    /** @var float */
    private $size;
    /** @var int */
    private $countActive;
    /** @var int */
    private $countTotal;
    /** @var string */
    private $chart;

    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @return $this
     */
    public function setDevice(string $device): self
    {
        $this->device = $device;

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

    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return $this
     */
    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Drive[]
     */
    public function getDrives(): array
    {
        return $this->drives;
    }

    /**
     * @param Drive[] $drives
     *
     * @return $this
     */
    public function setDrives(array $drives): self
    {
        $this->drives = $drives;

        return $this;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @return $this
     */
    public function setSize(float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getCountActive(): int
    {
        return $this->countActive;
    }

    /**
     * @return $this
     */
    public function setCountActive(int $countActive): self
    {
        $this->countActive = $countActive;

        return $this;
    }

    public function getCountTotal(): int
    {
        return $this->countTotal;
    }

    /**
     * @return $this
     */
    public function setCountTotal(int $countTotal): self
    {
        $this->countTotal = $countTotal;

        return $this;
    }

    public function getChart(): string
    {
        return $this->chart;
    }

    /**
     * @return $this
     */
    public function setChart(string $chart): self
    {
        $this->chart = $chart;

        return $this;
    }
}
