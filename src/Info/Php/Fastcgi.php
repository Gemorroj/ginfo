<?php

namespace Ginfo\Info\Php;

class Fastcgi
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
    private $totalProcesses;
    /** @var int|null */
    private $maxActiveProcesses;
    /** @var int|null */
    private $maxChildrenReached;
    /** @var int|null */
    private $slowRequests;
    /** @var FastcgiProcess[]|null */
    private $processes;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPool(): ?string
    {
        return $this->pool;
    }

    /**
     * @param string|null $pool
     *
     * @return $this
     */
    public function setPool(?string $pool): self
    {
        $this->pool = $pool;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProcessManager(): ?string
    {
        return $this->processManager;
    }

    /**
     * @param string|null $processManager
     *
     * @return $this
     */
    public function setProcessManager(?string $processManager): self
    {
        $this->processManager = $processManager;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime|null $startTime
     *
     * @return $this
     */
    public function setStartTime(?\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAcceptedConnections(): ?int
    {
        return $this->acceptedConnections;
    }

    /**
     * @param int|null $acceptedConnections
     *
     * @return $this
     */
    public function setAcceptedConnections(?int $acceptedConnections): self
    {
        $this->acceptedConnections = $acceptedConnections;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getListenQueue(): ?int
    {
        return $this->listenQueue;
    }

    /**
     * @param int|null $listenQueue
     *
     * @return $this
     */
    public function setListenQueue(?int $listenQueue): self
    {
        $this->listenQueue = $listenQueue;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxListenQueue(): ?int
    {
        return $this->maxListenQueue;
    }

    /**
     * @param int|null $maxListenQueue
     *
     * @return $this
     */
    public function setMaxListenQueue(?int $maxListenQueue): self
    {
        $this->maxListenQueue = $maxListenQueue;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getListenQueueLength(): ?int
    {
        return $this->listenQueueLength;
    }

    /**
     * @param int|null $listenQueueLength
     *
     * @return $this
     */
    public function setListenQueueLength(?int $listenQueueLength): self
    {
        $this->listenQueueLength = $listenQueueLength;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdleProcesses(): ?int
    {
        return $this->idleProcesses;
    }

    /**
     * @param int|null $idleProcesses
     *
     * @return $this
     */
    public function setIdleProcesses(?int $idleProcesses): self
    {
        $this->idleProcesses = $idleProcesses;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getActiveProcesses(): ?int
    {
        return $this->activeProcesses;
    }

    /**
     * @param int|null $activeProcesses
     *
     * @return $this
     */
    public function setActiveProcesses(?int $activeProcesses): self
    {
        $this->activeProcesses = $activeProcesses;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalProcesses(): ?int
    {
        return $this->totalProcesses;
    }

    /**
     * @param int|null $totalProcesses
     *
     * @return $this
     */
    public function setTotalProcesses(?int $totalProcesses): self
    {
        $this->totalProcesses = $totalProcesses;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxActiveProcesses(): ?int
    {
        return $this->maxActiveProcesses;
    }

    /**
     * @param int|null $maxActiveProcesses
     *
     * @return $this
     */
    public function setMaxActiveProcesses(?int $maxActiveProcesses): self
    {
        $this->maxActiveProcesses = $maxActiveProcesses;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxChildrenReached(): ?int
    {
        return $this->maxChildrenReached;
    }

    /**
     * @param int|null $maxChildrenReached
     *
     * @return $this
     */
    public function setMaxChildrenReached(?int $maxChildrenReached): self
    {
        $this->maxChildrenReached = $maxChildrenReached;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSlowRequests(): ?int
    {
        return $this->slowRequests;
    }

    /**
     * @param int|null $slowRequests
     *
     * @return $this
     */
    public function setSlowRequests(?int $slowRequests): self
    {
        $this->slowRequests = $slowRequests;

        return $this;
    }

    /**
     * @return FastcgiProcess[]|null
     */
    public function getProcesses(): ?array
    {
        return $this->processes;
    }

    /**
     * @param FastcgiProcess[]|null $processes
     *
     * @return $this
     */
    public function setProcesses(?array $processes): self
    {
        $this->processes = $processes;

        return $this;
    }
}
