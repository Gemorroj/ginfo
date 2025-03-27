<?php

namespace Ginfo\Info;

use Ginfo\Info\Samba\Connection;
use Ginfo\Info\Samba\File;

final class Samba
{
    /** @var File[] */
    private array $files = [];
    /** @var Samba\Service[] */
    private array $services = [];
    /** @var Connection[] */
    private array $connections = [];

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return Samba\Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param Samba\Service[] $services
     */
    public function setServices(array $services): self
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return Connection[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * @param Connection[] $connections
     */
    public function setConnections(array $connections): self
    {
        $this->connections = $connections;

        return $this;
    }
}
