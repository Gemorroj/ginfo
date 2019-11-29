<?php

namespace Ginfo\Info\Php;

class Fpm
{
    /** @var bool */
    private $enabled;
    /** @var string|null */
    private $pool;
    /** @var string|null */
    private $processManager;
    /** @var \DateTime|null */
    private $startTime;
    /** @var int|null */
    private $acceptedConnections;
    /** @var int|null */
    private $listenQueue;
    /** @var int|null */
    private $maxListenQueue;
    /** @var int|null */
    private $listenQueueLength;
    /** @var int|null */
    private $idleProcesses;
    /** @var int|null */
    private $activeProcesses;
    /** @var int|null */
    private $maxActiveProcesses;
    /** @var int|null */
    private $maxChildrenReached;
    /** @var int|null */
    private $slowRequests;
    /** @var FpmProcess[]|null */
    private $processes;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPool(): ?string
    {
        return $this->pool;
    }

    /**
     * @return $this
     */
    public function setPool(?string $pool): self
    {
        $this->pool = $pool;

        return $this;
    }

    public function getProcessManager(): ?string
    {
        return $this->processManager;
    }

    /**
     * @return $this
     */
    public function setProcessManager(?string $processManager): self
    {
        $this->processManager = $processManager;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    /**
     * @return $this
     */
    public function setStartTime(?\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getAcceptedConnections(): ?int
    {
        return $this->acceptedConnections;
    }

    /**
     * @return $this
     */
    public function setAcceptedConnections(?int $acceptedConnections): self
    {
        $this->acceptedConnections = $acceptedConnections;

        return $this;
    }

    public function getListenQueue(): ?int
    {
        return $this->listenQueue;
    }

    /**
     * @return $this
     */
    public function setListenQueue(?int $listenQueue): self
    {
        $this->listenQueue = $listenQueue;

        return $this;
    }

    public function getMaxListenQueue(): ?int
    {
        return $this->maxListenQueue;
    }

    /**
     * @return $this
     */
    public function setMaxListenQueue(?int $maxListenQueue): self
    {
        $this->maxListenQueue = $maxListenQueue;

        return $this;
    }

    public function getListenQueueLength(): ?int
    {
        return $this->listenQueueLength;
    }

    /**
     * @return $this
     */
    public function setListenQueueLength(?int $listenQueueLength): self
    {
        $this->listenQueueLength = $listenQueueLength;

        return $this;
    }

    public function getIdleProcesses(): ?int
    {
        return $this->idleProcesses;
    }

    /**
     * @return $this
     */
    public function setIdleProcesses(?int $idleProcesses): self
    {
        $this->idleProcesses = $idleProcesses;

        return $this;
    }

    public function getActiveProcesses(): ?int
    {
        return $this->activeProcesses;
    }

    /**
     * @return $this
     */
    public function setActiveProcesses(?int $activeProcesses): self
    {
        $this->activeProcesses = $activeProcesses;

        return $this;
    }

    public function getMaxActiveProcesses(): ?int
    {
        return $this->maxActiveProcesses;
    }

    /**
     * @return $this
     */
    public function setMaxActiveProcesses(?int $maxActiveProcesses): self
    {
        $this->maxActiveProcesses = $maxActiveProcesses;

        return $this;
    }

    public function getMaxChildrenReached(): ?int
    {
        return $this->maxChildrenReached;
    }

    /**
     * @return $this
     */
    public function setMaxChildrenReached(?int $maxChildrenReached): self
    {
        $this->maxChildrenReached = $maxChildrenReached;

        return $this;
    }

    public function getSlowRequests(): ?int
    {
        return $this->slowRequests;
    }

    /**
     * @return $this
     */
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
     *
     * @return $this
     */
    public function setProcesses(?array $processes): self
    {
        $this->processes = $processes;

        return $this;
    }
}
