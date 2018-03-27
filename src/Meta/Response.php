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
     * @return array
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
            'loggedUsers' => $this->os->getLoggedUsers(),
            'model' => $this->os->getModel(),
            //'selinux' => '', // todo: parse ini file /etc/sysconfig/selinux
        ];
    }

    /**
     * CPU info
     * @return array
     */
    public function getCpu()
    {
        $cpuInfo = $this->os->getCpu();
        return [
            'physical' => $cpuInfo['physical'],
            'virtual' => $cpuInfo['virtual'],
            'cores' => $cpuInfo['cores'],
            'hyperthreading' => $cpuInfo['cores'] < $cpuInfo['virtual'],

            'processor' => $cpuInfo['processor'], //physical processors info
            'load' => $this->os->getLoad(),
        ];
    }

    /**
     * Memory info
     * @return array
     */
    public function getMemory()
    {
        $memory = $this->os->getMemory();
        return [
            'memoryTotal' => $memory['memoryTotal'],
            'memoryUsed' => $memory['memoryUsed'],
            'memoryFree' => $memory['memoryFree'],
            'memoryShared' => $memory['memoryShared'],
            'memoryBuffers' => $memory['memoryBuffers'],
            'memoryCached' => $memory['memoryCached'],

            'swapTotal' => $memory['swapTotal'],
            'swapUsed' => $memory['swapUsed'],
            'swapFree' => $memory['swapFree'],
        ];
    }

    /**
     * USB devices
     */
    public function getUsb()
    {
        return $this->os->getUsb();
    }

    /**
     * PCI devices
     * @return array
     */
    public function getPci()
    {
        return $this->os->getPci();
    }

    /**
     * Sound cards
     * @return array
     */
    public function getSoundCard()
    {
        return $this->os->getSoundCards();
    }

    /**
     * Network devices and wifi
     * @return array
     */
    public function getNetwork()
    {
        return [
            'network' => $this->os->getNetwork(),
            'wifi' => $this->os->getWifi(),
        ];
    }

    /**
     * Battery status
     * @return array
     */
    public function getBattery()
    {
        return $this->os->getBattery();
    }


    /**
     * Hard disk info
     * @return array
     */
    public function getDisk()
    {
        return [
            'partitions' => $this->os->getPartitions(),
            'mounts' => $this->os->getMounts(),
            'raid' => $this->os->getRaid(),
        ];
    }

    /**
     * Temperatures
     * @return array
     */
    public function getTemps()
    {
        return $this->os->getTemps();
    }

    /**
     * Processes
     * @return array
     */
    public function getProcesses()
    {
        return $this->os->getProcesses();
    }

    /**
     * Services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->os->getServices();
    }
}
