<?php

namespace Ginfo\Info\Php;

final readonly class Fpm
{
    public function __construct(
        private bool $enabled,
        private ?string $pool = null,
        private ?string $processManager = null,
        private ?\DateTime $startTime = null,
        private ?int $acceptedConnections = null,
        private ?int $listenQueue = null,
        private ?int $maxListenQueue = null,
        private ?int $listenQueueLength = null,
        private ?int $idleProcesses = null,
        private ?int $activeProcesses = null,
        private ?int $maxActiveProcesses = null,
        private ?int $maxChildrenReached = null,
        private ?int $slowRequests = null,
        private array $processes = []
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPool(): ?string
    {
        return $this->pool;
    }

    public function getProcessManager(): ?string
    {
        return $this->processManager;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function getAcceptedConnections(): ?int
    {
        return $this->acceptedConnections;
    }

    public function getListenQueue(): ?int
    {
        return $this->listenQueue;
    }

    public function getMaxListenQueue(): ?int
    {
        return $this->maxListenQueue;
    }

    public function getListenQueueLength(): ?int
    {
        return $this->listenQueueLength;
    }

    public function getIdleProcesses(): ?int
    {
        return $this->idleProcesses;
    }

    public function getActiveProcesses(): ?int
    {
        return $this->activeProcesses;
    }

    public function getMaxActiveProcesses(): ?int
    {
        return $this->maxActiveProcesses;
    }

    public function getMaxChildrenReached(): ?int
    {
        return $this->maxChildrenReached;
    }

    public function getSlowRequests(): ?int
    {
        return $this->slowRequests;
    }

    /**
     * @return FpmProcess[]
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }
}
