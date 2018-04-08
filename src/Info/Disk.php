<?php

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
