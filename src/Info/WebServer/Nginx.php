<?php

namespace Ginfo\Info\WebServer;

use Ginfo\Info\InfoInterface;

final readonly class Nginx implements InfoInterface
{
    /**
     * @param NginxProcess[] $processes
     */
    public function __construct(
        private string $nginxVersion,
        private string $crypto,
        private bool $tlsSni,
        private string $args,
        private array $processes,
        private ?array $status = null,
    ) {
    }

    public function getNginxVersion(): string
    {
        return $this->nginxVersion;
    }

    public function getCrypto(): string
    {
        return $this->crypto;
    }

    public function isTlsSni(): bool
    {
        return $this->tlsSni;
    }

    public function getArgs(): string
    {
        return $this->args;
    }

    /**
     * @return NginxProcess[]
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }

    public function getStatus(): ?array
    {
        return $this->status;
    }
}
