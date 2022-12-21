<?php

namespace Ginfo\Info\Samba;

class File
{
    private int $pid;
    private string $user;
    private string $denyMode;
    private string $access;
    private string $rw;
    private string $oplock;
    private string $sharePath;
    private string $name;
    private \DateTime $time;

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDenyMode(): string
    {
        return $this->denyMode;
    }

    public function setDenyMode(string $denyMode): self
    {
        $this->denyMode = $denyMode;

        return $this;
    }

    public function getAccess(): string
    {
        return $this->access;
    }

    public function setAccess(string $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getRw(): string
    {
        return $this->rw;
    }

    public function setRw(string $rw): self
    {
        $this->rw = $rw;

        return $this;
    }

    public function getOplock(): string
    {
        return $this->oplock;
    }

    public function setOplock(string $oplock): self
    {
        $this->oplock = $oplock;

        return $this;
    }

    public function getSharePath(): string
    {
        return $this->sharePath;
    }

    public function setSharePath(string $sharePath): self
    {
        $this->sharePath = $sharePath;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTime(): \DateTime
    {
        return $this->time;
    }

    public function setTime(\DateTime $time): self
    {
        $this->time = $time;

        return $this;
    }
}
