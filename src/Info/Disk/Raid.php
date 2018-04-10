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
    private $count;
    /** @var string */
    private $chart;

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @param string $device
     * @return $this
     */
    public function setDevice(string $device): self
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
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
     * @return $this
     */
    public function setDrives(array $drives): self
    {
        $this->drives = $drives;
        return $this;
    }

    /**
     * @return float
     */
    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @param float $size
     * @return $this
     */
    public function setSize(float $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getChart(): string
    {
        return $this->chart;
    }

    /**
     * @param string $chart
     * @return $this
     */
    public function setChart(string $chart): self
    {
        $this->chart = $chart;
        return $this;
    }
}
