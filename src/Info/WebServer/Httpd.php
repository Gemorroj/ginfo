<?php

namespace Ginfo\Info\WebServer;

use Ginfo\Info\InfoInterface;

final readonly class Httpd implements InfoInterface
{
    /**
     * @param string[] $loaded
     */
    public function __construct(
        private string $version,
        private array $loaded,
        private string $mpm,
        private bool $threaded,
        private bool $forked,
        private string $args,
        private ?HttpdStatus $status = null,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLoaded(): array
    {
        return $this->loaded;
    }

    public function getMpm(): string
    {
        return $this->mpm;
    }

    public function isThreaded(): bool
    {
        return $this->threaded;
    }

    public function isForked(): bool
    {
        return $this->forked;
    }

    public function getArgs(): string
    {
        return $this->args;
    }

    public function getStatus(): ?HttpdStatus
    {
        return $this->status;
    }
}
