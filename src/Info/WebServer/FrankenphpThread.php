<?php

namespace Ginfo\Info\WebServer;

final readonly class FrankenphpThread
{
    public function __construct(
        private int $index,
        private string $name,
        private string $state,
        private bool $isWaiting,
        private bool $isBusy,
        private int $waitingSinceMilliseconds,
        private string $currentUri,
        private string $currentMethod,
        private int $requestStartedAt,
        private int $requestCount,
        private int $memoryUsage,
    ) {
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function isWaiting(): bool
    {
        return $this->isWaiting;
    }

    public function isBusy(): bool
    {
        return $this->isBusy;
    }

    public function getWaitingSinceMilliseconds(): int
    {
        return $this->waitingSinceMilliseconds;
    }

    public function getCurrentUri(): string
    {
        return $this->currentUri;
    }

    public function getCurrentMethod(): string
    {
        return $this->currentMethod;
    }

    /**
     * @return int Unix timestamp in milliseconds, 0 if the thread is idle
     */
    public function getRequestStartedAt(): int
    {
        return $this->requestStartedAt;
    }

    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    /**
     * @return int Memory usage in bytes
     */
    public function getMemoryUsage(): int
    {
        return $this->memoryUsage;
    }
}
