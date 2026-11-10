<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Clickhouse implements InfoInterface
{
    /**
     * @param array<string, int>   $metrics
     * @param array<string, int>   $events
     * @param array<string, float> $asynchronousMetrics
     * @param ClickhouseProcess[]  $processes
     * @param ClickhouseReplica[]  $replicas
     * @param ClickhouseDisk[]     $disks
     */
    public function __construct(
        private string $version,
        private int $uptime,
        private array $metrics,
        private array $events,
        private array $asynchronousMetrics,
        private array $processes,
        private array $replicas,
        private array $disks,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return int seconds
     */
    public function getUptime(): int
    {
        return $this->uptime;
    }

    /**
     * @return array<string, int>
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * @return array<string, int>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return array<string, float>
     */
    public function getAsynchronousMetrics(): array
    {
        return $this->asynchronousMetrics;
    }

    /**
     * @return ClickhouseProcess[]
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }

    /**
     * @return ClickhouseReplica[]
     */
    public function getReplicas(): array
    {
        return $this->replicas;
    }

    /**
     * @return ClickhouseDisk[]
     */
    public function getDisks(): array
    {
        return $this->disks;
    }
}
