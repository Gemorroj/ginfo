<?php

namespace Ginfo\Info\Disk;

use Ginfo\Info\Disk\Drive\Partition;

class Drive
{
    /** @var string */
    private $name;
    /** @var string|null */
    private $vendor;
    /** @var string */
    private $device;
    /** @var float|null */
    private $reads;
    /** @var float|null */
    private $writes;
    /** @var float */
    private $size;
    /** @var Partition[]|null */
    private $partitions;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    /**
     * @return $this
     */
    public function setVendor(?string $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

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

    public function getReads(): ?float
    {
        return $this->reads;
    }

    /**
     * @return $this
     */
    public function setReads(?float $reads): self
    {
        $this->reads = $reads;

        return $this;
    }

    public function getWrites(): ?float
    {
        return $this->writes;
    }

    /**
     * @return $this
     */
    public function setWrites(?float $writes): self
    {
        $this->writes = $writes;

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

    /**
     * @return Partition[]|null
     */
    public function getPartitions(): ?array
    {
        return $this->partitions;
    }

    /**
     * @param Partition[]|null $partitions
     *
     * @return $this
     */
    public function setPartitions(?array $partitions): self
    {
        $this->partitions = $partitions;

        return $this;
    }
}
