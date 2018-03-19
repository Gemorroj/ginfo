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
     * getPhpVersion
     *
     * @return string the version of php
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * getCPUArchitecture
     *
     * @return string the arch and bits
     */
    public function getCPUArchitecture()
    {
        return php_uname('m');
    }

    /**
     * getKernel
     *
     * @return string the OS kernel. A few OS classes override this.
     */
    public function getKernel()
    {
        return php_uname('r');
    }

    /**
     * getHostName
     *
     * @return string the OS' hostname A few OS classes override this.
     */
    public function getHostName()
    {
        // Take advantage of that function again
        return php_uname('n');
    }

    /**
     * getOS
     *
     * @return string the OS' name.
     */
    public function getOS()
    {
        $parts = explode('\\', get_class($this));
        return array_pop($parts);
    }
}
