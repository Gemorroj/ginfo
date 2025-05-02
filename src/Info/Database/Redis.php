<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Redis implements InfoInterface
{
    /**
     * @param array<string, string> $server
     * @param array<string, string> $clients
     * @param array<string, string> $memory
     * @param array<string, string> $persistence
     * @param array<string, string> $stats
     * @param array<string, string> $replication
     * @param array<string, string> $cpu
     * @param array<string, string> $modules
     * @param array<string, string> $errorstats
     * @param array<string, string> $cluster
     * @param array<string, string> $keyspace
     * @param array<string, string> $keysizes
     */
    public function __construct(
        private array $server,
        private array $clients,
        private array $memory,
        private array $persistence,
        private array $stats,
        private array $replication,
        private array $cpu,
        private array $modules,
        private array $errorstats,
        private array $cluster,
        private array $keyspace,
        private array $keysizes,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * @return array<string, string>
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    /**
     * @return array<string, string>
     */
    public function getMemory(): array
    {
        return $this->memory;
    }

    /**
     * @return array<string, string>
     */
    public function getPersistence(): array
    {
        return $this->persistence;
    }

    /**
     * @return array<string, string>
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * @return array<string, string>
     */
    public function getReplication(): array
    {
        return $this->replication;
    }

    /**
     * @return array<string, string>
     */
    public function getCpu(): array
    {
        return $this->cpu;
    }

    /**
     * @return array<string, string>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @return array<string, string>
     */
    public function getErrorstats(): array
    {
        return $this->errorstats;
    }

    /**
     * @return array<string, string>
     */
    public function getCluster(): array
    {
        return $this->cluster;
    }

    /**
     * @return array<string, string>
     */
    public function getKeyspace(): array
    {
        return $this->keyspace;
    }

    /**
     * @return array<string, string>
     */
    public function getKeysizes(): array
    {
        return $this->keysizes;
    }
}
