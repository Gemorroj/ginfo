<?php

namespace Ginfo\Info\Disk\Raid;

final readonly class Drive
{
    public function __construct(
        private string $path,
        private ?string $state = null
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getState(): ?string
    {
        return $this->state;
    }
}
