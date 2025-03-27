<?php

namespace Ginfo\Info\Samba;

final class Service
{
    private string $service;
    private int $pid;
    private string $machine;
    private \DateTime $connectedAt;
    private ?string $encryption = null;
    private ?string $signing = null;

    /**
     * @return string|null after samba 4.4
     */
    public function getEncryption(): ?string
    {
        return $this->encryption;
    }

    public function setEncryption(?string $encryption): self
    {
        $this->encryption = $encryption;

        return $this;
    }

    /**
     * @return string|null after samba 4.4
     */
    public function getSigning(): ?string
    {
        return $this->signing;
    }

    public function setSigning(?string $signing): self
    {
        $this->signing = $signing;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getMachine(): string
    {
        return $this->machine;
    }

    public function setMachine(string $machine): self
    {
        $this->machine = $machine;

        return $this;
    }

    public function getConnectedAt(): \DateTime
    {
        return $this->connectedAt;
    }

    public function setConnectedAt(\DateTime $connectedAt): self
    {
        $this->connectedAt = $connectedAt;

        return $this;
    }
}
