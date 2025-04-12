<?php

namespace Ginfo\Info;

final readonly class Process
{
    public function __construct(
        private string $name,
        private int $pid,
        private ?string $commandLine = null,
        private int $threads = 0,
        private ?string $state = null,
        private ?float $memory = null,
        private ?float $peakMemory = null,
        private ?string $user = null,
        private ?float $ioRead = null,
        private ?float $ioWrite = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCommandLine(): ?string
    {
        return $this->commandLine;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getMemory(): ?float
    {
        return $this->memory;
    }

    public function getPeakMemory(): ?float
    {
        return $this->peakMemory;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getIoRead(): ?float
    {
        return $this->ioRead;
    }

    public function getIoWrite(): ?float
    {
        return $this->ioWrite;
    }
}
