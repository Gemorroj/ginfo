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

class Sensor
{
    /** @var string|null */
    private $path;
    /** @var string */
    private $name;
    /** @var float */
    private $value; // temp
    /** @var string|null */
    private $unit; // C - celsius, F - Fahrenheit, V - Volt, W - Watt, RPM - revolution per minute, % - Percent

    /**
     * @return null|string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param null|string $path
     * @return $this
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;
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
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * @param null|string $unit
     * @return $this
     */
    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }
}
