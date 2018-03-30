<?php

/**
 * This file is part of Linfo (c) 2010 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo. If not, see <http://www.gnu.org/licenses/>.
 *
 */

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
