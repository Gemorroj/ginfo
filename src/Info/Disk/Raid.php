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

namespace Linfo\Info\Disk;

use Linfo\Info\Disk\Raid\Drive;

class Raid
{
    /** @var string */
    private $device;
    /** @var string */
    private $status;
    /** @var int */
    private $level;
    /** @var Drive[] */
    private $drives;
    /** @var float */
    private $size;
    /** @var int */
    private $count;
    /** @var string */
    private $chart;

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @param string $device
     * @return $this
     */
    public function setDevice(string $device): self
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel(int $level): self
    {
        $this->level = $level;
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
     * @return float
     */
    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @param float $size
     * @return $this
     */
    public function setSize(float $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getChart(): string
    {
        return $this->chart;
    }

    /**
     * @param string $chart
     * @return $this
     */
    public function setChart(string $chart): self
    {
        $this->chart = $chart;
        return $this;
    }
}
