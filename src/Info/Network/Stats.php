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

namespace Linfo\Info\Network;

class Stats
{
    /** @var int */
    private $bytes;
    /** @var int */
    private $errors;
    /** @var int */
    private $packets;

    /**
     * @return int
     */
    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @param int $bytes
     * @return $this
     */
    public function setBytes(int $bytes): self
    {
        $this->bytes = $bytes;
        return $this;
    }

    /**
     * @return int
     */
    public function getErrors(): int
    {
        return $this->errors;
    }

    /**
     * @param int $errors
     * @return $this
     */
    public function setErrors(int $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return int
     */
    public function getPackets(): int
    {
        return $this->packets;
    }

    /**
     * @param int $packets
     * @return $this
     */
    public function setPackets(int $packets): self
    {
        $this->packets = $packets;
        return $this;
    }

}
