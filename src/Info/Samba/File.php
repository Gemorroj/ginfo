<?php

namespace Ginfo\Info\Samba;

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

    public function getDenyMode(): string
    {
        return $this->denyMode;
    }

    /**
     * @return $this
     */
    public function setDenyMode(string $denyMode): self
    {
        $this->denyMode = $denyMode;

        return $this;
    }

    public function getAccess(): string
    {
        return $this->access;
    }

    /**
     * @return $this
     */
    public function setAccess(string $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getRw(): string
    {
        return $this->rw;
    }

    /**
     * @return $this
     */
    public function setRw(string $rw): self
    {
        $this->rw = $rw;

        return $this;
    }

    public function getOplock(): string
    {
        return $this->oplock;
    }

    /**
     * @return $this
     */
    public function setOplock(string $oplock): self
    {
        $this->oplock = $oplock;

        return $this;
    }

    public function getSharePath(): string
    {
        return $this->sharePath;
    }

    /**
     * @return $this
     */
    public function setSharePath(string $sharePath): self
    {
        $this->sharePath = $sharePath;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @return $this
     */
    public function setTime(\DateTime $time): self
    {
        $this->time = $time;

        return $this;
    }
}
