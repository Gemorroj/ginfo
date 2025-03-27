<?php

namespace Ginfo\Info\Disk;

final class Mount
{
    private string $device;
    private string $mount;
    private ?string $type = null;
    private ?float $size = null;
    private ?float $used = null;
    private ?float $free = null;
    private ?float $freePercent = null;
    private ?float $usedPercent = null;
    /** @var string[] */
    private array $options = [];

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getMount(): string
    {
        return $this->mount;
    }

    public function setMount(string $mount): self
    {
        $this->mount = $mount;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(?float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getUsed(): ?float
    {
        return $this->used;
    }

    public function setUsed(?float $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getFree(): ?float
    {
        return $this->free;
    }

    public function setFree(?float $free): self
    {
        $this->free = $free;

        return $this;
    }

    public function getFreePercent(): ?float
    {
        return $this->freePercent;
    }

    public function setFreePercent(?float $freePercent): self
    {
        $this->freePercent = $freePercent;

        return $this;
    }

    public function getUsedPercent(): ?float
    {
        return $this->usedPercent;
    }

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
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
