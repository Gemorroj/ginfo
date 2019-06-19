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

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     *
     * @return $this
     */
    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     *
     * @return $this
     */
    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequests(): int
    {
        return $this->requests;
    }

    /**
     * @param int $requests
     *
     * @return $this
     */
    public function setRequests(int $requests): self
    {
        $this->requests = $requests;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequestDuration(): int
    {
        return $this->requestDuration;
    }

    /**
     * @param int $requestDuration
     *
     * @return $this
     */
    public function setRequestDuration(int $requestDuration): self
    {
        $this->requestDuration = $requestDuration;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @param string $requestMethod
     *
     * @return $this
     */
    public function setRequestMethod(string $requestMethod): self
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    /**
     * @param string $requestUri
     *
     * @return $this
     */
    public function setRequestUri(string $requestUri): self
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * @param string $queryString
     *
     * @return $this
     */
    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;

        return $this;
    }

    /**
     * @return float
     */
    public function getRequestLength(): float
    {
        return $this->requestLength;
    }

    /**
     * @param float $requestLength
     *
     * @return $this
     */
    public function setRequestLength(float $requestLength): self
    {
        $this->requestLength = $requestLength;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return $this
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @param string $script
     *
     * @return $this
     */
    public function setScript(string $script): self
    {
        $this->script = $script;

        return $this;
    }

    /**
     * @return float
     */
    public function getLastRequestCpu(): float
    {
        return $this->lastRequestCpu;
    }

    /**
     * @param float $lastRequestCpu
     *
     * @return $this
     */
    public function setLastRequestCpu(float $lastRequestCpu): self
    {
        $this->lastRequestCpu = $lastRequestCpu;

        return $this;
    }

    /**
     * @return float
     */
    public function getLastRequestMemory(): float
    {
        return $this->lastRequestMemory;
    }

    /**
     * @param float $lastRequestMemory
     *
     * @return $this
     */
    public function setLastRequestMemory(float $lastRequestMemory): self
    {
        $this->lastRequestMemory = $lastRequestMemory;

        return $this;
    }
}
