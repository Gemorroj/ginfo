<?php

namespace Linfo\Info\Disk\Raid;

class Drive
{
    /** @var string */
    private $path;
    /** @var string|null */
    private $state;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param null|string $state
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }
}
