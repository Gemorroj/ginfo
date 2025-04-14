<?php

namespace Ginfo\Info\Samba;

final readonly class File
{
    public function __construct(
        private int $pid,
        private string $user,
        private string $denyMode,
        private string $access,
        private string $rw,
        private string $oplock,
        private string $sharePath,
        private string $name,
        private \DateTimeImmutable $time
    ) {
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getDenyMode(): string
    {
        return $this->denyMode;
    }

    public function getAccess(): string
    {
        return $this->access;
    }

    public function getRw(): string
    {
        return $this->rw;
    }

    public function getOplock(): string
    {
        return $this->oplock;
    }

    public function getSharePath(): string
    {
        return $this->sharePath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }
}
