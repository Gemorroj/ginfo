<?php

namespace Ginfo\Info;

use Ginfo\Info\Disk\Drive;
use Ginfo\Info\Disk\Mount;
use Ginfo\Info\Disk\Raid;

final class Disk
{
    /** @var Mount[]|null */
    private ?array $mounts = null;
    /** @var Drive[]|null */
    private ?array $drives = null;
    /** @var Raid[]|null */
    private ?array $raids = null;

    /**
     * @return Mount[]|null
     */
    public function getMounts(): ?array
    {
        return $this->mounts;
    }

    /**
     * @param Mount[]|null $mounts
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
     */
    public function setRaids(?array $raids): self
    {
        $this->raids = $raids;

        return $this;
    }
}
