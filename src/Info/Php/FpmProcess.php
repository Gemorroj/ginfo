<?php

namespace Ginfo\Info\Php;

class FpmProcess
{
    /** @var int */
    private $pid;
    /** @var string */
    private $state;
    /** @var \DateTime */
    private $startTime;
    /** @var int */
    private $requests;
    /** @var int */
    private $requestDuration;
    /** @var string */
    private $requestMethod;
    /** @var string */
    private $requestUri;
    /** @var string */
    private $queryString;
    /** @var float */
    private $requestLength;
    /** @var string */
    private $user;
    /** @var string */
    private $script;
    /** @var float */
    private $lastRequestCpu;
    /** @var float */
    private $lastRequestMemory;

    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @return $this
     */
    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @return $this
     */
    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getRequests(): int
    {
        return $this->requests;
    }

    /**
     * @return $this
     */
    public function setRequests(int $requests): self
    {
        $this->requests = $requests;

        return $this;
    }

    public function getRequestDuration(): int
    {
        return $this->requestDuration;
    }

    /**
     * @return $this
     */
    public function setRequestDuration(int $requestDuration): self
    {
        $this->requestDuration = $requestDuration;

        return $this;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @return $this
     */
    public function setRequestMethod(string $requestMethod): self
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    /**
     * @return $this
     */
    public function setRequestUri(string $requestUri): self
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * @return $this
     */
    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;

        return $this;
    }

    public function getRequestLength(): float
    {
        return $this->requestLength;
    }

    /**
     * @return $this
     */
    public function setRequestLength(float $requestLength): self
    {
        $this->requestLength = $requestLength;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @return $this
     */
    public function setScript(string $script): self
    {
        $this->script = $script;

        return $this;
    }

    public function getLastRequestCpu(): float
    {
        return $this->lastRequestCpu;
    }

    /**
     * @return $this
     */
    public function setLastRequestCpu(float $lastRequestCpu): self
    {
        $this->lastRequestCpu = $lastRequestCpu;

        return $this;
    }

    public function getLastRequestMemory(): float
    {
        return $this->lastRequestMemory;
    }

    /**
     * @return $this
     */
    public function setLastRequestMemory(float $lastRequestMemory): self
    {
        $this->lastRequestMemory = $lastRequestMemory;

        return $this;
    }
}
