<?php

namespace Ginfo\Info\Php;

final readonly class FpmProcess
{
    public function __construct(
        private int $pid,
        private string $state,
        private \DateTime $startTime,
        private int $requests,
        private int $requestDuration,
        private string $requestMethod,
        private string $requestUri,
        private string $queryString,
        private float $requestLength,
        private string $user,
        private string $script,
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

    public function getRequestDuration(): int
    {
        return $this->requestDuration;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getRequestLength(): float
    {
        return $this->requestLength;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getScript(): string
    {
        return $this->script;
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
