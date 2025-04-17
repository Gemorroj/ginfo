<?php

namespace Ginfo\Info;

use Ginfo\Info\Disk\Drive;
use Ginfo\Info\Disk\Mount;
use Ginfo\Info\Disk\Raid;

final readonly class Disk implements InfoInterface
{
    public function __construct(
        /** @var Mount[] */
        private array $mounts,
        /** @var Drive[] */
        private array $drives,
        /** @var Raid[] */
        private array $raids,
    ) {
    }

    /**
     * @return Mount[]
     */
    public function getMounts(): array
    {
        return $this->mounts;
    }

    /**
     * @return Drive[]
     */
    public function getDrives(): array
    {
        return $this->drives;
    }

    /**
     * @return Raid[]
     */
    public function getRaids(): array
    {
        return $this->raids;
    }
}
