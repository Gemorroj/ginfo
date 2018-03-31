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

namespace Linfo;

use Linfo\Info\Battery;
use Linfo\Info\Cpu;
use Linfo\Info\General;
use Linfo\Info\Memory;
use Linfo\Info\Network;
use Linfo\Info\Pci;
use Linfo\Info\Process;
use Linfo\Info\Samba;
use Linfo\Info\Selinux;
use Linfo\Info\Sensor;
use Linfo\Info\Service;
use Linfo\Info\SoundCard;
use Linfo\Info\Usb;
use Linfo\OS\OS;

class Info
{
    /** @var OS */
    private $os;

    /**
     * @param OS $os
     */
    public function __construct(OS $os)
    {
        $this->os = $os;
    }

    /**
     * General info
     * @return General
     */
    public function getGeneral()
    {
        return (new General())
            ->setDate(new \DateTime())
            ->setUptime($this->os->getUptime())
            ->setOsName($this->os->getOsName())
            ->setKernel($this->os->getKernel())
            ->setHostname($this->os->getHostName())
            ->setArchitecture($this->os->getArchitecture())
            ->setVirtualization($this->os->getVirtualization())
            ->setModel($this->os->getModel())
            ->setLoggedUsers($this->os->getLoggedUsers())
            ->setLoad($this->os->getLoad());
    }

    /**
     * CPU info
     * @return Cpu|null
     */
    public function getCpu() : ?Cpu
    {
        return $this->os->getCpu();
    }

    /**
     * Memory info
     * @return Memory|null
     */
    public function getMemory() : ?Memory
    {
        return $this->os->getMemory();
    }

    /**
     * USB devices
     * @return Usb[]|null
     */
    public function getUsb() : ?array
    {
        return $this->os->getUsb();
    }

    /**
     * PCI devices
     * @return Pci[]|null
     */
    public function getPci() : ?array
    {
        return $this->os->getPci();
    }

    /**
     * Sound cards
     * @return SoundCard[]|null
     */
    public function getSoundCard() : ?array
    {
        return $this->os->getSoundCards();
    }

    /**
     * Network devices
     * @return Network[]|null
     */
    public function getNetwork()
    {
        return $this->os->getNetwork();
    }

    /**
     * Battery status
     * @return Battery[]|null
     */
    public function getBattery() : ?array
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
     * Temperatures|Voltages
     * @return Sensor[]|null
     */
    public function getSensors()
    {
        return $this->os->getSensors();
    }

    /**
     * Processes
     * @return Process[]|null
     */
    public function getProcesses()
    {
        return $this->os->getProcesses();
    }

    /**
     * Services
     *
     * @return Service[]|null
     */
    public function getServices() : ?array
    {
        return $this->os->getServices();
    }


    /**
     * UPS status
     *
     * @return array|null
     */
    public function getUps()
    {
        return $this->os->getUps();
    }

    /**
     * Printers
     *
     * @return array|null
     */
    public function getPrinters()
    {
        return $this->os->getPrinters();
    }


    /**
     * Samba status
     *
     * @return Samba|null
     */
    public function getSamba()
    {
        return $this->os->getSamba();
    }


    /**
     * Selinux status
     *
     * @return Selinux|null
     */
    public function getSelinux() : ?Selinux
    {
        return $this->os->getSelinux();
    }
}
