<?php

namespace Ginfo\Info\Disk;

use Ginfo\Info\Disk\Drive\Partition;

class Drive
{
    private string $name;
    private ?string $vendor = null;
    private string $device;
    private ?float $reads = null;
    private ?float $writes = null;
    private float $size;
    /** @var Partition[]|null */
    private ?array $partitions = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getReads(): ?float
    {
        return $this->reads;
    }

    public function setReads(?float $reads): self
    {
        $this->reads = $reads;

        return $this;
    }

    public function getWrites(): ?float
    {
        return $this->writes;
    }

    public function setWrites(?float $writes): self
    {
        $this->writes = $writes;

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

    /**
     * @return Partition[]|null
     */
    public function getPartitions(): ?array
    {
        return $this->partitions;
    }

    /**
     * @param Partition[]|null $partitions
     */
    public function setPartitions(?array $partitions): self
    {
        $this->partitions = $partitions;

        return $this;
    }
}
