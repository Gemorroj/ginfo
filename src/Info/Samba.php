<?php

namespace Linfo\Info;

use Linfo\Info\Samba\Connection;
use Linfo\Info\Samba\File;

class Samba
{
    /** @var File[] */
    private $files;
    /** @var \Linfo\Info\Samba\Service[] */
    private $services;
    /** @var Connection[] */
    private $connections;

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setConnections(array $connections): self
    {
        $this->connections = $connections;
        return $this;
    }
}
