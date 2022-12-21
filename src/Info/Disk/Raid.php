<?php

namespace Ginfo\Info\Disk;

use Ginfo\Info\Disk\Raid\Drive;

class Raid
{
    private string $device;
    private string $status;
    private int $level;
    /** @var Drive[] */
    private array $drives = [];
    private float $size;
    private int $countActive;
    private int $countTotal;
    private string $chart;

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

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

    public function getLevel(): int
    {
        return $this->level;
    }

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

    public function setSize(float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getCountActive(): int
    {
        return $this->countActive;
    }

    public function setCountActive(int $countActive): self
    {
        $this->countActive = $countActive;

        return $this;
    }

    public function getCountTotal(): int
    {
        return $this->countTotal;
    }

    public function setCountTotal(int $countTotal): self
    {
        $this->countTotal = $countTotal;

        return $this;
    }

    public function getChart(): string
    {
        return $this->chart;
    }

    public function setChart(string $chart): self
    {
        $this->chart = $chart;

        return $this;
    }
}
