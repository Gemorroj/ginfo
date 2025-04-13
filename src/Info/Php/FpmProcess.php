<?php

namespace Ginfo\Info\Php;

final readonly class FpmProcess
{
    public function __construct(
        private int $pid,
        private string $state,
        private \DateTime $startTime,
        private int $requests,
        private int $lastRequestDuration,
        private string $lastRequestMethod,
        private string $lastRequestUri,
        private string $lastRequestQueryString,
        private float $lastRequestLength,
        private string $lastRequestUser,
        private string $lastRequestScript,
        private float $lastRequestCpu,
        private float $lastRequestMemory,
    ) {
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function getRequests(): int
    {
        return $this->requests;
    }

    public function getLastRequestDuration(): int
    {
        return $this->lastRequestDuration;
    }

    public function getLastRequestMethod(): string
    {
        return $this->lastRequestMethod;
    }

    public function getLastRequestUri(): string
    {
        return $this->lastRequestUri;
    }

    public function getLastRequestQueryString(): string
    {
        return $this->lastRequestQueryString;
    }

    public function getLastRequestLength(): float
    {
        return $this->lastRequestLength;
    }

    public function getLastRequestUser(): string
    {
        return $this->lastRequestUser;
    }

    public function getLastRequestScript(): string
    {
        return $this->lastRequestScript;
    }

    public function getLastRequestCpu(): float
    {
        return $this->lastRequestCpu;
    }

    public function getLastRequestMemory(): float
    {
        return $this->lastRequestMemory;
    }
}
