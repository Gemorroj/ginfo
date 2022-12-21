<?php

namespace Ginfo\Info;

class General
{
    private \DateTime $date;
    private string $osName;
    private string $kernel;
    private string $hostName;
    private ?\DateInterval $uptime = null;
    private string $architecture;
    private ?string $virtualization = null;
    /** @var string[]|null */
    private ?array $loggedUsers = null;

    private ?string $model = null;
    /** @var float[]|null */
    private ?array $load = null;

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOsName(): string
    {
        return $this->osName;
    }

    public function setOsName(string $osName): self
    {
        $this->osName = $osName;

        return $this;
    }

    public function getKernel(): string
    {
        return $this->kernel;
    }

    public function setKernel(string $kernel): self
    {
        $this->kernel = $kernel;

        return $this;
    }

    public function getHostname(): string
    {
        return $this->hostName;
    }

    public function setHostName(string $hostName): self
    {
        $this->hostName = $hostName;

        return $this;
    }

    public function getUptime(): ?\DateInterval
    {
        return $this->uptime;
    }

    /**
     * @param \DateInterval|float|null $uptime
     */
    public function setUptime($uptime): self
    {
        if (\is_numeric($uptime)) {
            $startDate = new \DateTime('now - '.$uptime.' seconds');
            $endDate = new \DateTime('now');

            $this->uptime = $startDate->diff($endDate);
        } elseif ($uptime instanceof \DateInterval) {
            $this->uptime = $uptime;
        } elseif (null === $uptime) {
            $this->uptime = null;
        } else {
            throw new \InvalidArgumentException('Incorrect uptime format.');
        }

        return $this;
    }

    public function getArchitecture(): string
    {
        return $this->architecture;
    }

    public function setArchitecture(string $architecture): self
    {
        $this->architecture = $architecture;

        return $this;
    }

    public function getVirtualization(): ?string
    {
        return $this->virtualization;
    }

    public function setVirtualization(?string $virtualization): self
    {
        $this->virtualization = $virtualization;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLoggedUsers(): ?array
    {
        return $this->loggedUsers;
    }

    /**
     * @param string[]|null $loggedUsers
     */
    public function setLoggedUsers(?array $loggedUsers): self
    {
        $this->loggedUsers = $loggedUsers;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return float[]|null
     */
    public function getLoad(): ?array
    {
        return $this->load;
    }

    /**
     * @param float[]|null $load
     */
    public function setLoad(?array $load): self
    {
        $this->load = $load;

        return $this;
    }
}
