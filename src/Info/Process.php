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

class Process
{
    /** @var string */
    private $name;
    /** @var string|null */
    private $commandLine;
    /** @var int */
    private $threads;
    /** @var string|null */
    private $state;
    /** @var float */
    private $memory;
    /** @var float */
    private $peakMemory;
    /** @var int */
    private $pid;
    /** @var string|null */
    private $user;
    /** @var float|null */
    private $ioRead;
    /** @var float|null */
    private $ioWrite;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCommandLine(): ?string
    {
        return $this->commandLine;
    }

    /**
     * @param null|string $commandLine
     * @return $this
     */
    public function setCommandLine(?string $commandLine): self
    {
        $this->commandLine = $commandLine;
        return $this;
    }

    /**
     * @return int
     */
    public function getThreads(): int
    {
        return $this->threads;
    }

    /**
     * @param int $threads
     * @return $this
     */
    public function setThreads(int $threads): self
    {
        $this->threads = $threads;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return float
     */
    public function getMemory(): float
    {
        return $this->memory;
    }

    /**
     * @param float $memory
     * @return $this
     */
    public function setMemory(float $memory): self
    {
        $this->memory = $memory;
        return $this;
    }

    /**
     * @return float
     */
    public function getPeakMemory(): float
    {
        return $this->peakMemory;
    }

    /**
     * @param float $peakMemory
     * @return $this
     */
    public function setPeakMemory(float $peakMemory): self
    {
        $this->peakMemory = $peakMemory;
        return $this;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     * @return $this
     */
    public function setPid(int $pid): self
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string|null $user
     * @return $this
     */
    public function setUser(?string $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getIoRead(): ?float
    {
        return $this->ioRead;
    }

    /**
     * @param float|null $ioRead
     * @return $this
     */
    public function setIoRead(?float $ioRead): self
    {
        $this->ioRead = $ioRead;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getIoWrite(): ?float
    {
        return $this->ioWrite;
    }

    /**
     * @param float|null $ioWrite
     * @return $this
     */
    public function setIoWrite(?float $ioWrite): self
    {
        $this->ioWrite = $ioWrite;
        return $this;
    }
}
