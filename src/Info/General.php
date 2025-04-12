<?php

namespace Ginfo\Info;

final readonly class General
{
    public function __construct(
        private \DateTime $date,
        private string $osName,
        private string $kernel,
        private string $hostName,
        private string $architecture,
        private ?\DateInterval $uptime = null,
        private ?string $virtualization = null,
        /** @var string[]|null */
        private ?array $loggedUsers = null,
        private ?string $model = null,
        /** @var float[]|null */
        private ?array $load = null,
    ) {
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getOsName(): string
    {
        return $this->osName;
    }

    public function getKernel(): string
    {
        return $this->kernel;
    }

    public function getHostname(): string
    {
        return $this->hostName;
    }

    public function getUptime(): ?\DateInterval
    {
        return $this->uptime;
    }

    public function getArchitecture(): string
    {
        return $this->architecture;
    }

    public function getVirtualization(): ?string
    {
        return $this->virtualization;
    }

    /**
     * @return string[]|null
     */
    public function getLoggedUsers(): ?array
    {
        return $this->loggedUsers;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @return float[]|null
     */
    public function getLoad(): ?array
    {
        return $this->load;
    }
}
