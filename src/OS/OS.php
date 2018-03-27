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
     * @return string the version of php
     */
    public function getPhpVersion()
    {
        return \phpversion();
    }

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
     * @return int seconds
     */
    public abstract function getUptime();

    /**
     * @return string
     */
    public abstract function getVirtualization();

    /**
     * @return array
     */
    public abstract function getCpu();

    /**
     * @return array
     */
    public abstract function getLoad();

    /**
     * @return array
     */
    public abstract function getMemory();

    /**
     * @return array
     */
    public abstract function getSoundCards();

    /**
     * @return string[]
     */
    public abstract function getLoggedUsers();

    /**
     * Get brand/name of motherboard/server
     *
     * return string
     */
    public abstract function getModel();

    /**
     * @return array
     */
    public abstract function getUsb();

    /**
     * @return array
     */
    public abstract function getPci();

    /**
     * @return array
     */
    public abstract function getNetwork();
}
