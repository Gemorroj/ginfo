<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Manticore implements InfoInterface
{
    /**
     * @param array<string, string> $globalVariables
     * @param array<string, string> $status
     * @param array<string, string> $settings
     * @param array<string, string> $agentStatus
     */
    public function __construct(
        private array $globalVariables,
        private array $status,
        private array $settings,
        private array $agentStatus,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getGlobalVariables(): array
    {
        return $this->globalVariables;
    }

    /**
     * @return array<string, string>
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @return array<string, string>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return array<string, string>
     */
    public function getAgentStatus(): array
    {
        return $this->agentStatus;
    }
}
