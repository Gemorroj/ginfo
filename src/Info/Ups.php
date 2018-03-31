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

class Ups
{
    /** @var string */
    private $name;
    /** @var string */
    private $model;
    /** @var float */
    private $batteryVolts;
    /** @var float */
    private $batteryCharge;
    /** @var int */
    private $timeLeft;
    /** @var float */
    private $currentLoad;
    /** @var string */
    private $status;

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
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return float Volt
     */
    public function getBatteryVolts(): float
    {
        return $this->batteryVolts;
    }

    /**
     * @param float $batteryVolts
     * @return $this
     */
    public function setBatteryVolts(float $batteryVolts): self
    {
        $this->batteryVolts = $batteryVolts;
        return $this;
    }

    /**
     * @return float Percent
     */
    public function getBatteryCharge(): float
    {
        return $this->batteryCharge;
    }

    /**
     * @param float $batteryCharge
     * @return $this
     */
    public function setBatteryCharge(float $batteryCharge): self
    {
        $this->batteryCharge = $batteryCharge;
        return $this;
    }

    /**
     * @return int Seconds
     */
    public function getTimeLeft(): int
    {
        return $this->timeLeft;
    }

    /**
     * @param int $timeLeft
     * @return $this
     */
    public function setTimeLeft(int $timeLeft): self
    {
        $this->timeLeft = $timeLeft;
        return $this;
    }

    /**
     * @return float Percent
     */
    public function getCurrentLoad(): float
    {
        return $this->currentLoad;
    }

    /**
     * @param float $currentLoad
     * @return $this
     */
    public function setCurrentLoad(float $currentLoad): self
    {
        $this->currentLoad = $currentLoad;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }


}
