<?php

namespace Ginfo\Info\Disk\Raid;

class Drive
{
    /** @var string */
    private $path;
    /** @var string|null */
    private $state;

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
