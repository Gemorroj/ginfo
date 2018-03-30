<?php

/**
 * This file is part of Linfo (c) 2010 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Linfo\Info\Samba;

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
     * @return null|string after samba 4.4
     */
    public function getEncryption(): ?string
    {
        return $this->encryption;
    }

    /**
     * @param null|string $encryption
     * @return $this
     */
    public function setEncryption(?string $encryption): self
    {
        $this->encryption = $encryption;
        return $this;
    }

    /**
     * @return null|string after samba 4.4
     */
    public function getSigning(): ?string
    {
        return $this->signing;
    }

    /**
     * @param null|string $signing
     * @return $this
     */
    public function setSigning(?string $signing): self
    {
        $this->signing = $signing;
        return $this;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     * @return $this
     */
    public function setPid(int $pid): self
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     * @return $this
     */
    public function setGroup(string $group): self
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
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
     * @param string $protocolVersion
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion): self
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }
}
