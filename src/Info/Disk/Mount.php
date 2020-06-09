<?php

namespace Ginfo\Info\Disk;

class Mount
{
    /** @var string */
    private $device;
    /** @var string */
    private $mount;
    /** @var string|null */
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

    public function getMount(): string
    {
        return $this->mount;
    }

    /**
     * @return $this
     */
    public function setMount(string $mount): self
    {
        $this->mount = $mount;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    /**
     * @return $this
     */
    public function setSize(?float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getUsed(): ?float
    {
        return $this->used;
    }

    /**
     * @return $this
     */
    public function setUsed(?float $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getFree(): ?float
    {
        return $this->free;
    }

    /**
     * @return $this
     */
    public function setFree(?float $free): self
    {
        $this->free = $free;

        return $this;
    }

    public function getFreePercent(): ?float
    {
        return $this->freePercent;
    }

    /**
     * @return $this
     */
    public function setFreePercent(?float $freePercent): self
    {
        $this->freePercent = $freePercent;

        return $this;
    }

    public function getUsedPercent(): ?float
    {
        return $this->usedPercent;
    }

    /**
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
