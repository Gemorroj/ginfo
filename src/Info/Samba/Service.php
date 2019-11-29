<?php

namespace Ginfo\Info\Samba;

class Service
{
    /** @var string */
    private $service;
    /** @var int */
    private $pid;
    /** @var string */
    private $machine;
    /** @var \DateTime */
    private $connectedAt;
    /** @var string|null */
    private $encryption;
    /** @var string|null */
    private $signing;

    /**
     * @return string|null after samba 4.4
     */
    public function getEncryption(): ?string
    {
        return $this->encryption;
    }

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
    public function setSigning(?string $signing): self
    {
        $this->signing = $signing;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @return $this
     */
    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

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

    public function getMachine(): string
    {
        return $this->machine;
    }

    /**
     * @return $this
     */
    public function setMachine(string $machine): self
    {
        $this->machine = $machine;

        return $this;
    }

    public function getConnectedAt(): \DateTime
    {
        return $this->connectedAt;
    }

    /**
     * @return $this
     */
    public function setConnectedAt(\DateTime $connectedAt): self
    {
        $this->connectedAt = $connectedAt;

        return $this;
    }
}
