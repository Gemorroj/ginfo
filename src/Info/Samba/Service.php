<?php

namespace Ginfo\Info\Samba;

final readonly class Service
{
    public function __construct(
        private string $service,
        private int $pid,
        private string $machine,
        private \DateTime $connectedAt,
        private ?string $encryption = null,
        private ?string $signing = null
    ) {
    }

    /**
     * @return string|null after samba 4.4
     */
    public function getEncryption(): ?string
    {
        return $this->encryption;
    }

    /**
     * @return string|null after samba 4.4
     */
    public function getSigning(): ?string
    {
        return $this->signing;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getMachine(): string
    {
        return $this->machine;
    }

    public function getConnectedAt(): \DateTime
    {
        return $this->connectedAt;
    }
}
