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

namespace Linfo\Info;

class General
{
    /** @var \DateTime */
    private $date;
    /** @var string */
    private $osName;
    /** @var string */
    private $kernel;
    /** @var string */
    private $hostName;
    /** @var \DateInterval|null */
    private $uptime;
    /** @var string */
    private $architecture;
    /** @var string|null */
    private $virtualization;
    /** @var string[]|null */
    private $loggedUsers;
    /** @var string|null */
    private $model;
    /** @var float[]|null */
    private $load;

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getOsName(): string
    {
        return $this->osName;
    }

    /**
     * @param string $osName
     * @return $this
     */
    public function setOsName(string $osName): self
    {
        $this->osName = $osName;
        return $this;
    }

    /**
     * @return string
     */
    public function getKernel(): string
    {
        return $this->kernel;
    }

    /**
     * @param string $kernel
     * @return $this
     */
    public function setKernel(string $kernel): self
    {
        $this->kernel = $kernel;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return $this->hostName;
    }

    /**
     * @param string $hostName
     * @return $this
     */
    public function setHostName(string $hostName): self
    {
        $this->hostName = $hostName;
        return $this;
    }

    /**
     * @return \DateInterval|null
     */
    public function getUptime(): ?\DateInterval
    {
        return $this->uptime;
    }

    /**
     * @param \DateInterval|int|null $uptime
     * @return $this
     */
    public function setUptime($uptime): self
    {
        if (\is_numeric($uptime)) {
            $startDate = new \DateTime('now - ' . $uptime . ' seconds');
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

    /**
     * @return string
     */
    public function getArchitecture(): string
    {
        return $this->architecture;
    }

    /**
     * @param string $architecture
     * @return $this
     */
    public function setArchitecture(string $architecture): self
    {
        $this->architecture = $architecture;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVirtualization(): ?string
    {
        return $this->virtualization;
    }

    /**
     * @param string|null $virtualization
     * @return $this
     */
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
     * @return $this
     */
    public function setLoggedUsers(?array $loggedUsers): self
    {
        $this->loggedUsers = $loggedUsers;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string|null $model
     * @return $this
     */
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
     * @return $this
     */
    public function setLoad(?array $load): self
    {
        $this->load = $load;
        return $this;
    }
}
