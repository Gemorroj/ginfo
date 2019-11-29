<?php

namespace Ginfo\Info;

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
    /** @var float|null */
    private $memory;
    /** @var float|null */
    private $peakMemory;
    /** @var int */
    private $pid;
    /** @var string|null */
    private $user;
    /** @var float|null */
    private $ioRead;
    /** @var float|null */
    private $ioWrite;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCommandLine(): ?string
    {
        return $this->commandLine;
    }

    /**
     * @return $this
     */
    public function setCommandLine(?string $commandLine): self
    {
        $this->commandLine = $commandLine;

        return $this;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    /**
     * @return $this
     */
    public function setThreads(int $threads): self
    {
        $this->threads = $threads;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getMemory(): ?float
    {
        return $this->memory;
    }

    /**
     * @return $this
     */
    public function setMemory(?float $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getPeakMemory(): ?float
    {
        return $this->peakMemory;
    }

    /**
     * @return $this
     */
    public function setPeakMemory(?float $peakMemory): self
    {
        $this->peakMemory = $peakMemory;

        return $this;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @return $this
     */
    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIoRead(): ?float
    {
        return $this->ioRead;
    }

    /**
     * @return $this
     */
    public function setIoRead(?float $ioRead): self
    {
        $this->ioRead = $ioRead;

        return $this;
    }

    public function getIoWrite(): ?float
    {
        return $this->ioWrite;
    }

    /**
     * @return $this
     */
    public function setIoWrite(?float $ioWrite): self
    {
        $this->ioWrite = $ioWrite;

        return $this;
    }
}
