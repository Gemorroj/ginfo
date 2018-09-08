<?php

namespace Ginfo\Info\Disk;

class Mount
{
    /** @var string */
    private $device;
    /** @var string */
    private $mount;
    /** @var string */
    private $type;
    /** @var float|null */
    private $size;
    /** @var float|null */
    private $used;
    /** @var float|null */
    private $free;
    /** @var float|null */
    private $freePercent;
    /** @var float|null */
    private $usedPercent;
    /** @var string[] */
    private $options;

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
     * @return string
     */
    public function getMount(): string
    {
        return $this->mount;
    }

    /**
     * @param string $mount
     *
     * @return $this
     */
    public function setMount(string $mount): self
    {
        $this->mount = $mount;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getSize(): ?float
    {
        return $this->size;
    }

    /**
     * @param float|null $size
     *
     * @return $this
     */
    public function setSize(?float $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getUsed(): ?float
    {
        return $this->used;
    }

    /**
     * @param float|null $used
     *
     * @return $this
     */
    public function setUsed(?float $used): self
    {
        $this->used = $used;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getFree(): ?float
    {
        return $this->free;
    }

    /**
     * @param float|null $free
     *
     * @return $this
     */
    public function setFree(?float $free): self
    {
        $this->free = $free;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getFreePercent(): ?float
    {
        return $this->freePercent;
    }

    /**
     * @param float|null $freePercent
     *
     * @return $this
     */
    public function setFreePercent(?float $freePercent): self
    {
        $this->freePercent = $freePercent;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getUsedPercent(): ?float
    {
        return $this->usedPercent;
    }

    /**
     * @param float|null $usedPercent
     *
     * @return $this
     */
    public function setUsedPercent(?float $usedPercent): self
    {
        $this->usedPercent = $usedPercent;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string[] $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
