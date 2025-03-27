<?php

namespace Ginfo\Info\Php;

final class Fpm
{
    private bool $enabled;
    private ?string $pool = null;
    private ?string $processManager = null;
    private ?\DateTime $startTime = null;
    private ?int $acceptedConnections = null;
    private ?int $listenQueue = null;
    private ?int $maxListenQueue = null;
    private ?int $listenQueueLength = null;
    private ?int $idleProcesses = null;
    private ?int $activeProcesses = null;
    private ?int $maxActiveProcesses = null;
    private ?int $maxChildrenReached = null;
    private ?int $slowRequests = null;
    private ?array $processes = null;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPool(): ?string
    {
        return $this->pool;
    }

    public function setPool(?string $pool): self
    {
        $this->pool = $pool;

        return $this;
    }

    public function getProcessManager(): ?string
    {
        return $this->processManager;
    }

    public function setProcessManager(?string $processManager): self
    {
        $this->processManager = $processManager;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getAcceptedConnections(): ?int
    {
        return $this->acceptedConnections;
    }

    public function setAcceptedConnections(?int $acceptedConnections): self
    {
        $this->acceptedConnections = $acceptedConnections;

        return $this;
    }

    public function getListenQueue(): ?int
    {
        return $this->listenQueue;
    }

    public function setListenQueue(?int $listenQueue): self
    {
        $this->listenQueue = $listenQueue;

        return $this;
    }

    public function getMaxListenQueue(): ?int
    {
        return $this->maxListenQueue;
    }

    public function setMaxListenQueue(?int $maxListenQueue): self
    {
        $this->maxListenQueue = $maxListenQueue;

        return $this;
    }

    public function getListenQueueLength(): ?int
    {
        return $this->listenQueueLength;
    }

    public function setListenQueueLength(?int $listenQueueLength): self
    {
        $this->listenQueueLength = $listenQueueLength;

        return $this;
    }

    public function getIdleProcesses(): ?int
    {
        return $this->idleProcesses;
    }

    public function setIdleProcesses(?int $idleProcesses): self
    {
        $this->idleProcesses = $idleProcesses;

        return $this;
    }

    public function getActiveProcesses(): ?int
    {
        return $this->activeProcesses;
    }

    public function setActiveProcesses(?int $activeProcesses): self
    {
        $this->activeProcesses = $activeProcesses;

        return $this;
    }

    public function getMaxActiveProcesses(): ?int
    {
        return $this->maxActiveProcesses;
    }

    public function setMaxActiveProcesses(?int $maxActiveProcesses): self
    {
        $this->maxActiveProcesses = $maxActiveProcesses;

        return $this;
    }

    public function getMaxChildrenReached(): ?int
    {
        return $this->maxChildrenReached;
    }

    public function setMaxChildrenReached(?int $maxChildrenReached): self
    {
        $this->maxChildrenReached = $maxChildrenReached;

        return $this;
    }

    public function getSlowRequests(): ?int
    {
        return $this->slowRequests;
    }

    public function setSlowRequests(?int $slowRequests): self
    {
        $this->slowRequests = $slowRequests;

        return $this;
    }

    /**
     * @return FpmProcess[]|null
     */
    public function getProcesses(): ?array
    {
        return $this->processes;
    }

    /**
     * @param FpmProcess[]|null $processes
     */
    public function setProcesses(?array $processes): self
    {
        $this->processes = $processes;

        return $this;
    }
}
