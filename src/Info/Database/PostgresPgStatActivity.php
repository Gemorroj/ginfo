<?php

namespace Ginfo\Info\Database;

// https://postgrespro.ru/docs/postgresql/17/monitoring-stats#MONITORING-PG-STAT-ACTIVITY-VIEW
final readonly class PostgresPgStatActivity
{
    public function __construct(
        private ?int $datid,
        private ?string $datname,
        private int $pid,
        private ?int $leaderPid,
        private ?int $usesysid,
        private ?string $usename,
        private ?string $applicationName,
        private ?string $clientAddr,
        private ?string $clientHostname,
        private ?int $clientPort,
        private \DateTimeImmutable $backendStart,
        private ?\DateTimeImmutable $xactStart,
        private ?\DateTimeImmutable $queryStart,
        private ?\DateTimeImmutable $stateChange,
        private ?string $waitEventType,
        private ?string $waitEvent,
        private ?string $state,
        private ?int $backendXid,
        private ?int $backendXmin,
        private int|float|null $queryId,
        private ?string $query,
        private string $backendType,
    ) {
    }

    public function getDatid(): ?int
    {
        return $this->datid;
    }

    public function getDatname(): ?string
    {
        return $this->datname;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getLeaderPid(): ?int
    {
        return $this->leaderPid;
    }

    public function getUsesysid(): ?int
    {
        return $this->usesysid;
    }

    public function getUsename(): ?string
    {
        return $this->usename;
    }

    public function getApplicationName(): ?string
    {
        return $this->applicationName;
    }

    public function getClientAddr(): ?string
    {
        return $this->clientAddr;
    }

    public function getClientHostname(): ?string
    {
        return $this->clientHostname;
    }

    public function getClientPort(): ?int
    {
        return $this->clientPort;
    }

    public function getBackendStart(): \DateTimeImmutable
    {
        return $this->backendStart;
    }

    public function getXactStart(): ?\DateTimeImmutable
    {
        return $this->xactStart;
    }

    public function getQueryStart(): ?\DateTimeImmutable
    {
        return $this->queryStart;
    }

    public function getStateChange(): ?\DateTimeImmutable
    {
        return $this->stateChange;
    }

    public function getWaitEventType(): ?string
    {
        return $this->waitEventType;
    }

    public function getWaitEvent(): ?string
    {
        return $this->waitEvent;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getBackendXid(): ?int
    {
        return $this->backendXid;
    }

    public function getBackendXmin(): ?int
    {
        return $this->backendXmin;
    }

    public function getQueryId(): int|float|null
    {
        return $this->queryId;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getBackendType(): string
    {
        return $this->backendType;
    }
}
