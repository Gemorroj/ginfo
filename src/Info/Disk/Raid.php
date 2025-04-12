<?php

namespace Ginfo\Info\Disk;

use Ginfo\Info\Disk\Raid\Drive;

final readonly class Raid
{
    public function __construct(
        private string $device,
        private string $status,
        private int $level,
        private float $size,
        private int $countActive,
        private int $countTotal,
        private string $chart,
        /** @var Drive[] */
        private array $drives = [],
    ) {
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return Drive[]
     */
    public function getDrives(): array
    {
        return $this->drives;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function getCountActive(): int
    {
        return $this->countActive;
    }

    public function getCountTotal(): int
    {
        return $this->countTotal;
    }

    public function getChart(): string
    {
        return $this->chart;
    }
}
