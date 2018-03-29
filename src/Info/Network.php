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

use Linfo\Info\Network\Stats;

class Network
{
    /** @var string */
    private $name;
    /** @var int|null */
    private $portSpeed;
    /** @var string|null */
    private $type;
    /** @var string|null */
    private $state;
    /** @var Stats|null */
    private $statsReceived;
    /** @var Stats|null */
    private $statsSent;

    /**
     * @return Stats|null
     */
    public function getStatsReceived(): ?Stats
    {
        return $this->statsReceived;
    }

    /**
     * @param Stats|null $statsReceived
     * @return $this
     */
    public function setStatsReceived(?Stats $statsReceived): self
    {
        $this->statsReceived = $statsReceived;
        return $this;
    }

    /**
     * @return Stats|null
     */
    public function getStatsSent(): ?Stats
    {
        return $this->statsSent;
    }

    /**
     * @param Stats|null $statsSent
     * @return $this
     */
    public function setStatsSent(?Stats $statsSent): self
    {
        $this->statsSent = $statsSent;
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
     * @return int|null
     */
    public function getPortSpeed(): ?int
    {
        return $this->portSpeed;
    }

    /**
     * @param int|null $portSpeed
     * @return $this
     */
    public function setPortSpeed(?int $portSpeed): self
    {
        $this->portSpeed = $portSpeed;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }
}
