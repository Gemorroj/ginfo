<?php

namespace Ginfo\Info;

use Ginfo\Info\Samba\Connection;
use Ginfo\Info\Samba\File;

final readonly class Samba
{
    public function __construct(
        /** @var File[] */
        private array $files = [],
        /** @var Samba\Service[] */
        private array $services = [],
        /** @var Connection[] */
        private array $connections = []
    ) {
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return Samba\Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return Connection[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }
}
