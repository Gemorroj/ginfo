<?php

namespace Ginfo\Info;

final readonly class Apparmor implements InfoInterface
{
    /**
     * @param array<string, string>                                                            $profiles  profile name => mode
     * @param array<int, array{executable: string, profile: string, pid: int, status: string}> $processes
     */
    public function __construct(
        private bool $enabled,
        private array $profiles,
        private array $processes,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array<string, string> profile name => mode (enforce|complain|prompt|kill|unconfined)
     */
    public function getProfiles(): array
    {
        return $this->profiles;
    }

    /**
     * @return array<int, array{executable: string, profile: string, pid: int, status: string}>
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }
}
