<?php

namespace Ginfo\Info\Database;

final readonly class ClickhouseDisk
{
    public function __construct(
        private string $name,
        private string $path,
        private int $freeSpace,
        private int $totalSpace,
        private int $keepFreeSpace,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFreeSpace(): int
    {
        return $this->freeSpace;
    }

    public function getTotalSpace(): int
    {
        return $this->totalSpace;
    }

    public function getKeepFreeSpace(): int
    {
        return $this->keepFreeSpace;
    }
}
