<?php

namespace Ginfo\Info;

class Process
{
    private string $name;
    private ?string $commandLine = null;
    private int $threads = 0;
    private ?string $state = null;
    private ?float $memory = null;
    private ?float $peakMemory = null;
    private int $pid;
    private ?string $user = null;
    private ?float $ioRead = null;
    private ?float $ioWrite = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCommandLine(): ?string
    {
        return $this->commandLine;
    }

    public function setCommandLine(?string $commandLine): self
    {
        $this->commandLine = $commandLine;

        return $this;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    public function setThreads(int $threads): self
    {
        $this->threads = $threads;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getMemory(): ?float
    {
        return $this->memory;
    }

    public function setMemory(?float $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getPeakMemory(): ?float
    {
        return $this->peakMemory;
    }

    public function setPeakMemory(?float $peakMemory): self
    {
        $this->peakMemory = $peakMemory;

        return $this;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIoRead(): ?float
    {
        return $this->ioRead;
    }

    public function setIoRead(?float $ioRead): self
    {
        $this->ioRead = $ioRead;

        return $this;
    }

    public function getIoWrite(): ?float
    {
        return $this->ioWrite;
    }

    public function setIoWrite(?float $ioWrite): self
    {
        $this->ioWrite = $ioWrite;

        return $this;
    }
}
