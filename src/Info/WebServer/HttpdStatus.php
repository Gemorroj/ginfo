<?php

namespace Ginfo\Info\WebServer;

final readonly class HttpdStatus
{
    /**
     * @param array{value: float, unit: string} $totalTraffic
     * @param array{value: int, unit: string}   $sslSharedMemory
     */
    public function __construct(
        private string $uptime,
        private string $load,
        private int $totalAccesses,
        private array $totalTraffic,
        private int $totalDuration,
        private float $requestsSec,
        private int $bSecond,
        private int $bRequest,
        private float $msRequest,
        private int $requestsCurrentlyProcessed,
        private int $workersGracefullyRestarting,
        private int $idleWorkers,
        private string $sslCacheType,
        private array $sslSharedMemory,
    ) {
    }

    public function getUptime(): string
    {
        return $this->uptime;
    }

    public function getLoad(): string
    {
        return $this->load;
    }

    public function getTotalAccesses(): int
    {
        return $this->totalAccesses;
    }

    /**
     * @return array{value: float, unit: string}
     */
    public function getTotalTraffic(): array
    {
        return $this->totalTraffic;
    }

    public function getTotalDuration(): int
    {
        return $this->totalDuration;
    }

    public function getRequestsSec(): float
    {
        return $this->requestsSec;
    }

    public function getBSecond(): int
    {
        return $this->bSecond;
    }

    public function getBRequest(): int
    {
        return $this->bRequest;
    }

    public function getMsRequest(): float
    {
        return $this->msRequest;
    }

    public function getRequestsCurrentlyProcessed(): int
    {
        return $this->requestsCurrentlyProcessed;
    }

    public function getWorkersGracefullyRestarting(): int
    {
        return $this->workersGracefullyRestarting;
    }

    public function getIdleWorkers(): int
    {
        return $this->idleWorkers;
    }

    public function getSslCacheType(): string
    {
        return $this->sslCacheType;
    }

    /**
     * @return array{value: int, unit: string}
     */
    public function getSslSharedMemory(): array
    {
        return $this->sslSharedMemory;
    }
}
