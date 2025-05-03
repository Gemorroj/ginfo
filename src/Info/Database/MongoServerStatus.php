<?php

namespace Ginfo\Info\Database;

final readonly class MongoServerStatus
{
    public function __construct(
        private string $host,
        private string $version,
        private string $process,
        private int $pid,
        private int|float $uptime,
        private \DateTimeImmutable $localTime,
        private int|float $pageFaults,
        private int $usagePageFileMB,
        private int $totalPageFileMB,
        private int $availPageFileMB,
        private int $ramMB,
        private MongoServerStatusNetwork $network,
        private MongoServerStatusCounters $counters,
        private MongoServerStatusConnections $connections,
    ) {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getProcess(): string
    {
        return $this->process;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getUptime(): float|int
    {
        return $this->uptime;
    }

    public function getLocalTime(): \DateTimeImmutable
    {
        return $this->localTime;
    }

    public function getPageFaults(): float|int
    {
        return $this->pageFaults;
    }

    public function getUsagePageFileMB(): int
    {
        return $this->usagePageFileMB;
    }

    public function getTotalPageFileMB(): int
    {
        return $this->totalPageFileMB;
    }

    public function getAvailPageFileMB(): int
    {
        return $this->availPageFileMB;
    }

    public function getRamMB(): int
    {
        return $this->ramMB;
    }

    public function getNetwork(): MongoServerStatusNetwork
    {
        return $this->network;
    }

    public function getCounters(): MongoServerStatusCounters
    {
        return $this->counters;
    }

    public function getConnections(): MongoServerStatusConnections
    {
        return $this->connections;
    }
}
