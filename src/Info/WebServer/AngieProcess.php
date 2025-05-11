<?php

namespace Ginfo\Info\WebServer;

final readonly class AngieProcess
{
    public function __construct(
        private int $pid,
        private bool $master,
        private ?float $vmPeak,
        private ?float $vmSize,
        private ?int $uptime,
    ) {
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function isMaster(): bool
    {
        return $this->master;
    }

    public function getVmPeak(): ?float
    {
        return $this->vmPeak;
    }

    public function getVmSize(): ?float
    {
        return $this->vmSize;
    }

    public function getUptime(): ?int
    {
        return $this->uptime;
    }
}
