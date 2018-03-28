<?php

/**
 * This file is part of Linfo (c) 2014 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo.    If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Linfo\OS;


abstract class OS
{
    /**
     * @return string the arch OS
     */
    public function getArchitecture() : string
    {
        return \php_uname('m');
    }

    /**
     * @return string the OS kernel. A few OS classes override this.
     */
    public function getKernel() : string
    {
        return \php_uname('r');
    }

    /**
     * @return string the OS' hostname A few OS classes override this.
     */
    public function getHostName() : string
    {
        return \php_uname('n');
    }

    /**
     * @return string the OS' name.
     */
    public abstract function getOsName() : string;

    /**
     * @return int|null seconds
     */
    public abstract function getUptime() : ?int;

    /**
     * @return string|null
     */
    public abstract function getVirtualization() : ?string;

    /**
     * @return array|null
     */
    public abstract function getCpu() : ?array;

    /**
     * @return array|null
     */
    public abstract function getLoad() : ?array;

    /**
     * @return array|null
     */
    public abstract function getMemory() : ?array;

    /**
     * @return array|null
     */
    public abstract function getSoundCards() : ?array;

    /**
     * @return string[]|null
     */
    public abstract function getLoggedUsers() : ?array;

    /**
     * Get brand/name of motherboard/server
     *
     * @return string|null
     */
    public abstract function getModel() : ?string;

    /**
     * @return array|null
     */
    public abstract function getUsb() : ?array;

    /**
     * @return array|null
     */
    public abstract function getPci() : ?array;

    /**
     * @return array|null
     */
    public abstract function getNetwork() : ?array;

    /**
     * @return array|null
     */
    public abstract function getPartitions() : ?array;

    /**
     * @return array|null
     */
    public abstract function getMounts() : ?array;

    /**
     * @return array|null
     */
    public abstract function getRaid() : ?array;

    /**
     * @return array|null
     */
    public abstract function getBattery() : ?array;

    /**
     * @return array|null
     */
    public abstract function getWifi() : ?array;

    /**
     * @return array|null
     */
    public abstract function getTemps() : ?array;

    /**
     * @return array|null
     */
    public abstract function getProcesses() : ?array;

    /**
     * @return array|null
     */
    public abstract function getServices() : ?array;

    /**
     * @return array|null
     */
    public abstract function getUps() : ?array;

    /**
     * @return array|null
     */
    public abstract function getPrinters() : ?array;

    /**
     * @return array|null
     */
    public abstract function getSamba() : ?array;

    /**
     * @return array|null
     */
    public abstract function getSelinux() : ?array;
}
