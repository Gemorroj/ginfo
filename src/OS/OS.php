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
    public function getArchitecture()
    {
        return \php_uname('m');
    }

    /**
     * @return string the OS kernel. A few OS classes override this.
     */
    public function getKernel()
    {
        return \php_uname('r');
    }

    /**
     * @return string the OS' hostname A few OS classes override this.
     */
    public function getHostName()
    {
        return \php_uname('n');
    }

    /**
     * @return string the OS' name.
     */
    public abstract function getOsName();

    /**
     * @return int|null seconds
     */
    public abstract function getUptime();

    /**
     * @return string|null
     */
    public abstract function getVirtualization();

    /**
     * @return array|null
     */
    public abstract function getCpu();

    /**
     * @return array|null
     */
    public abstract function getLoad();

    /**
     * @return array
     */
    public abstract function getMemory();

    /**
     * @return array|null
     */
    public abstract function getSoundCards();

    /**
     * @return string[]|null
     */
    public abstract function getLoggedUsers();

    /**
     * Get brand/name of motherboard/server
     *
     * return string|null
     */
    public abstract function getModel();

    /**
     * @return array|null
     */
    public abstract function getUsb();

    /**
     * @return array|null
     */
    public abstract function getPci();

    /**
     * @return array|null
     */
    public abstract function getNetwork();

    /**
     * @return array|null
     */
    public abstract function getPartitions();

    /**
     * @return array|null
     */
    public abstract function getMounts();

    /**
     * @return array|null
     */
    public abstract function getRaid();

    /**
     * @return array|null
     */
    public abstract function getBattery();

    /**
     * @return array|null
     */
    public abstract function getWifi();

    /**
     * @return array|null
     */
    public abstract function getTemps();

    /**
     * @return array|null
     */
    public abstract function getProcesses();

    /**
     * @return array|null
     */
    public abstract function getServices();

    /**
     * @return array|null
     */
    public abstract function getUps();

    /**
     * @return array|null
     */
    public abstract function getPrinters();

    /**
     * @return array|null
     */
    public abstract function getSamba();

    /**
     * @return array|null
     */
    public abstract function getSelinux();
}
