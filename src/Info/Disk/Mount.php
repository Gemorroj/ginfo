<?php

namespace Ginfo\Info\Disk;

final readonly class Mount
{
    public function __construct(
        private string $device,
        private string $mount,
        private ?string $type = null,
        private ?float $size = null,
        private ?float $used = null,
        private ?float $free = null,
        private ?float $freePercent = null,
        private ?float $usedPercent = null,
        /** @var string[] */
        private array $options = [],
    ) {
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function getMount(): string
    {
        return $this->mount;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function getUsed(): ?float
    {
        return $this->used;
    }

    public function getFree(): ?float
    {
        return $this->free;
    }

    public function getFreePercent(): ?float
    {
        return $this->freePercent;
    }

    public function getUsedPercent(): ?float
    {
        return $this->usedPercent;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
