<?php

namespace Ginfo\Info;

use Ginfo\Info\Disk\Drive;
use Ginfo\Info\Disk\Mount;
use Ginfo\Info\Disk\Raid;

class Disk
{
    /** @var Mount[]|null */
    private $mounts;
    /** @var Drive[]|null */
    private $drives;
    /** @var Raid[]|null */
    private $raids;

    /**
     * @return Mount[]|null
     */
    public function getMounts(): ?array
    {
        return $this->mounts;
    }

    /**
     * @param Mount[]|null $mounts
     *
     * @return $this
     */
    public function setMounts(?array $mounts): self
    {
        $this->mounts = $mounts;

        return $this;
    }

    /**
     * @return Drive[]|null
     */
    public function getDrives(): ?array
    {
        return $this->drives;
    }

    /**
     * @param Drive[]|null $drives
     *
     * @return $this
     */
    public function setDrives(?array $drives): self
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
     *
     * @return $this
     */
    public function setRaids(?array $raids): self
    {
        $this->raids = $raids;

        return $this;
    }
}
