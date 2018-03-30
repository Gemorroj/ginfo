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

namespace Linfo\Info\Cpu;

class Processor
{
    /** @var string */
    private $model;
    /** @var float */
    private $speed;
    /** @var int */
    private $l2Cache;
    /** @var string[]|null */
    private $flags;
    /** @var string */
    private $architecture;

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpeed(): float
    {
        return $this->speed;
    }

    /**
     * @param float $speed
     * @return $this
     */
    public function setSpeed(float $speed): self
    {
        $this->speed = $speed;
        return $this;
    }

    /**
     * @return int
     */
    public function getL2Cache(): int
    {
        return $this->l2Cache;
    }

    /**
     * @param int $l2Cache
     * @return $this
     */
    public function setL2Cache(int $l2Cache): self
    {
        $this->l2Cache = $l2Cache;
        return $this;
    }

    /**
     * @see https://unix.stackexchange.com/questions/43539/what-do-the-flags-in-proc-cpuinfo-mean#answer-43540
     * @return null|string[]
     */
    public function getFlags(): ?array
    {
        return $this->flags;
    }

    /**
     * @param null|string[] $flags
     * @return $this
     */
    public function setFlags(?array $flags): self
    {
        $this->flags = $flags;
        return $this;
    }

    /**
     * @param string $architecture (x86|x64|ia64) currently arm or mips not supported
     * @return $this
     */
    public function setArchitecture(string $architecture): self
    {
        $this->architecture = $architecture;
        return $this;
    }

    /**
     * @return string (x86|x64|ia64) currently arm or mips not supported
     */
    public function getArchitecture()
    {
        return $this->architecture;
    }
}
