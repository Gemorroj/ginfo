<?php

namespace Ginfo\Info\Samba;

final readonly class Connection
{
    public function __construct(
        private int $pid,
        private string $user,
        private string $group,
        private string $host,
        private string $ip,
        private string $protocolVersion,
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

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string after samba 4
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }
}
