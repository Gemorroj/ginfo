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

class File
{
    /** @var int */
    private $pid;
    /** @var string */
    private $user;
    /** @var string */
    private $denyMode;
    /** @var string */
    private $access;
    /** @var string */
    private $rw;
    /** @var string */
    private $oplock;
    /** @var string */
    private $sharePath;
    /** @var string */
    private $name;
    /** @var \DateTime */
    private $time;

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
    public function getDenyMode(): string
    {
        return $this->denyMode;
    }

    /**
     * @param string $denyMode
     * @return $this
     */
    public function setDenyMode(string $denyMode): self
    {
        $this->denyMode = $denyMode;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccess(): string
    {
        return $this->access;
    }

    /**
     * @param string $access
     * @return $this
     */
    public function setAccess(string $access): self
    {
        $this->access = $access;
        return $this;
    }

    /**
     * @return string
     */
    public function getRw(): string
    {
        return $this->rw;
    }

    /**
     * @param string $rw
     * @return $this
     */
    public function setRw(string $rw): self
    {
        $this->rw = $rw;
        return $this;
    }

    /**
     * @return string
     */
    public function getOplock(): string
    {
        return $this->oplock;
    }

    /**
     * @param string $oplock
     * @return $this
     */
    public function setOplock(string $oplock): self
    {
        $this->oplock = $oplock;
        return $this;
    }

    /**
     * @return string
     */
    public function getSharePath(): string
    {
        return $this->sharePath;
    }

    /**
     * @param string $sharePath
     * @return $this
     */
    public function setSharePath(string $sharePath): self
    {
        $this->sharePath = $sharePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     * @return $this
     */
    public function setTime(\DateTime $time): self
    {
        $this->time = $time;
        return $this;
    }


}
