<?php

namespace Ginfo\Info\WebServer;

use Ginfo\Info\InfoInterface;

final readonly class Angie implements InfoInterface
{
    /**
     * @param AngieProcess[] $processes
     */
    public function __construct(
        private string $angieVersion,
        private string $nginxVersion,
        private ?\DateTimeImmutable $buildDate,
        private string $crypto,
        private bool $tlsSni,
        private string $args,
        private array $processes,
        private ?array $status = null,
    ) {
    }

    public function getAngieVersion(): string
    {
        return $this->angieVersion;
    }

    public function getNginxVersion(): string
    {
        return $this->nginxVersion;
    }

    public function getBuildDate(): ?\DateTimeImmutable
    {
        return $this->buildDate;
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
     * @return AngieProcess[]
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
