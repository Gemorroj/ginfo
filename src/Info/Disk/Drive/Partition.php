<?php

namespace Ginfo\Info\Disk\Drive;

final readonly class Partition
{
    public function __construct(
        private float $size,
        private string $name
    ) {
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
