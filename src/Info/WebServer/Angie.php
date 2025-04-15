<?php

namespace Ginfo\Info\WebServer;

final readonly class Angie
{
    public function __construct(
        private string $angieVersion,
        private string $nginxVersion,
        private ?\DateTimeImmutable $buildDate,
        private string $crypto,
        private bool $tlsSni,
        private string $args,
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

    public function getStatus(): ?array
    {
        return $this->status;
    }
}
