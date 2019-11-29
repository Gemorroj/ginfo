<?php

namespace Ginfo\Info\Samba;

class Connection
{
    /** @var int */
    private $pid;
    /** @var string */
    private $user;
    /** @var string */
    private $group;
    /** @var string */
    private $host;
    /** @var string */
    private $ip;
    /** @var string */
    private $protocolVersion;
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

    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return $this
     */
    public function setGroup(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return $this
     */
    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string after samba 4
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion): self
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }
}
