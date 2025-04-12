<?php

namespace Ginfo\Info\Disk;

use Ginfo\Info\Disk\Drive\Partition;

final readonly class Drive
{
    public function __construct(
        private string $name,
        private string $device,
        private float $size,
        private ?string $vendor = null,
        private ?float $reads = null,
        private ?float $writes = null,
        /** @var Partition[] */
        private array $partitions = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function getReads(): ?float
    {
        return $this->reads;
    }

    public function getWrites(): ?float
    {
        return $this->writes;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @return Partition[]
     */
    public function getPartitions(): array
    {
        return $this->partitions;
    }
}
