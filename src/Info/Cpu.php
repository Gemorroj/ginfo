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

use Linfo\Info\Cpu\Processor;

class Cpu
{
    /** @var Processor[] */
    private $processors;
    /** @var int */
    private $physical;
    /** @var int */
    private $cores;
    /** @var int */
    private $virtual;
    /** @var bool */
    private $hyperThreading;

    /**
     * @return Processor[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * @param Processor[] $processors
     * @return $this
     */
    public function setProcessors(array $processors): self
    {
        $this->processors = $processors;
        return $this;
    }

    /**
     * @param Processor $processor
     * @return $this
     */
    public function addProcessor(Processor $processor): self
    {
        $this->processors[] = $processor;
        return $this;
    }

    /**
     * @return int
     */
    public function getPhysical(): int
    {
        return $this->physical;
    }

    /**
     * @param int $physical
     * @return $this
     */
    public function setPhysical(int $physical): self
    {
        $this->physical = $physical;
        return $this;
    }

    /**
     * @return int
     */
    public function getCores(): int
    {
        return $this->cores;
    }

    /**
     * @param int $cores
     * @return $this
     */
    public function setCores(int $cores): self
    {
        $this->cores = $cores;
        return $this;
    }

    /**
     * @return int
     */
    public function getVirtual(): int
    {
        return $this->virtual;
    }

    /**
     * @param int $virtual
     * @return $this
     */
    public function setVirtual(int $virtual): self
    {
        $this->virtual = $virtual;
        return $this;
    }


    /**
     * @return bool
     */
    public function getHyperThreading(): bool
    {
        return $this->hyperThreading;
    }

    /**
     * @param bool $hyperThreading
     * @return $this
     */
    public function setHyperThreading(bool $hyperThreading): self
    {
        $this->hyperThreading = $hyperThreading;
        return $this;
    }
}
