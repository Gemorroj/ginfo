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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    /**
     * @param null|string $vendor
     *
     * @return $this
     */
    public function setVendor(?string $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @param string $device
     *
     * @return $this
     */
    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getReads(): ?float
    {
        return $this->reads;
    }

    /**
     * @param float|null $reads
     *
     * @return $this
     */
    public function setReads(?float $reads): self
    {
        $this->reads = $reads;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getWrites(): ?float
    {
        return $this->writes;
    }

    /**
     * @param float|null $writes
     *
     * @return $this
     */
    public function setWrites(?float $writes): self
    {
        $this->writes = $writes;

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
     *
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
