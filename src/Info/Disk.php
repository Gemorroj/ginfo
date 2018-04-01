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

use Linfo\Info\Disk\Mount;
use Linfo\Info\Disk\Drive;
use Linfo\Info\Disk\Raid;

class Disk
{
    /** @var Mount[] */
    private $mounts;
    /** @var Drive[] */
    private $drives;
    /** @var Raid[]|null */
    private $raids;

    /**
     * @return Mount[]
     */
    public function getMounts(): array
    {
        return $this->mounts;
    }

    /**
     * @param Mount[] $mounts
     * @return $this
     */
    public function setMounts(array $mounts): self
    {
        $this->mounts = $mounts;
        return $this;
    }

    /**
     * @return Drive[]
     */
    public function getDrives(): array
    {
        return $this->drives;
    }

    /**
     * @param Drive[] $drives
     * @return $this
     */
    public function setDrives(array $drives): self
    {
        $this->drives = $drives;
        return $this;
    }

    /**
     * @return Raid[]|null
     */
    public function getRaids(): ?array
    {
        return $this->raids;
    }

    /**
     * @param Raid[]|null $raids
     * @return $this
     */
    public function setRaids(?array $raids): self
    {
        $this->raids = $raids;
        return $this;
    }
}
