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

namespace Linfo\Meta;

use Linfo\OS\OS;

class Response extends \ArrayObject //fixes for old api
{
    /** @var OS */
    private $os;
    public function __construct(OS $os)
    {
        $this->os = $os;
    }

    /**
     * General info
     */
    public function getGeneral()
    {
        return [
            'datetime' => new \DateTime(),
            'osName' => $this->os->getOsName(),
            'kernel' => $this->os->getKernel(),
            'hostname' => $this->os->getHostName(),
            'uptime' => $this->os->getUptime(),
            'architecture' => $this->os->getArchitecture(),
            'virtualization' => $this->os->getVirtualization(),
            //'selinux' => '', // todo: parse ini file /etc/sysconfig/selinux
        ];
    }

    /**
     * CPU info
     */
    public function getCpu()
    {
        //todo
    }

    /**
     * Memory info
     */
    public function getMemory()
    {
        //todo
    }

    public function getDisk()
    {
        //todo
    }

    public function getNetwork()
    {
        //todo
    }

    public function getUsb()
    {
        //todo
    }

    public function getPci()
    {
        //todo
    }

    public function getSoundCard()
    {
        //todo
    }

    public function getProcess()
    {
        //todo
    }
}
