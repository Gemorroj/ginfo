<?php

namespace Ginfo\Info\Php;

class FpmProcess
{
    private int $pid;
    private string $state;
    private \DateTime $startTime;
    private int $requests;
    private int $requestDuration;
    private string $requestMethod;
    private string $requestUri;
    private string $queryString;
    private float $requestLength;
    private string $user;
    private string $script;
    private float $lastRequestCpu;
    private float $lastRequestMemory;

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getRequests(): int
    {
        return $this->requests;
    }

    public function setRequests(int $requests): self
    {
        $this->requests = $requests;

        return $this;
    }

    public function getRequestDuration(): int
    {
        return $this->requestDuration;
    }

    public function setRequestDuration(int $requestDuration): self
    {
        $this->requestDuration = $requestDuration;

        return $this;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function setRequestMethod(string $requestMethod): self
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    public function setRequestUri(string $requestUri): self
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;

        return $this;
    }

    public function getRequestLength(): float
    {
        return $this->requestLength;
    }

    public function setRequestLength(float $requestLength): self
    {
        $this->requestLength = $requestLength;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getScript(): string
    {
        return $this->script;
    }

    public function setScript(string $script): self
    {
        $this->script = $script;

        return $this;
    }

    public function getLastRequestCpu(): float
    {
        return $this->lastRequestCpu;
    }

    public function setLastRequestCpu(float $lastRequestCpu): self
    {
        $this->lastRequestCpu = $lastRequestCpu;

        return $this;
    }

    public function getLastRequestMemory(): float
    {
        return $this->lastRequestMemory;
    }

    public function setLastRequestMemory(float $lastRequestMemory): self
    {
        $this->lastRequestMemory = $lastRequestMemory;

        return $this;
    }
}
