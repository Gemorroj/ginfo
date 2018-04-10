<?php

namespace Ginfo;

use Ginfo\Info\Battery;
use Ginfo\Info\Cpu;
use Ginfo\Info\Disk;
use Ginfo\Info\General;
use Ginfo\Info\Memory;
use Ginfo\Info\Network;
use Ginfo\Info\Pci;
use Ginfo\Info\Printer;
use Ginfo\Info\Process;
use Ginfo\Info\Samba;
use Ginfo\Info\Selinux;
use Ginfo\Info\Sensor;
use Ginfo\Info\Service;
use Ginfo\Info\SoundCard;
use Ginfo\Info\Ups;
use Ginfo\Info\Usb;
use Ginfo\OS\OS;

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
    public function getGeneral() : General
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
    public function getNetwork() : ?array
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
     * @return Disk
     */
    public function getDisk() : Disk
    {
        return (new Disk())
            ->setMounts($this->os->getMounts())
            ->setDrives($this->os->getDrives())
            ->setRaids($this->os->getRaids());
    }

    /**
     * Temperatures|Voltages
     * @return Sensor[]|null
     */
    public function getSensors() : ?array
    {
        return $this->os->getSensors();
    }

    /**
     * Processes
     * @return Process[]|null
     */
    public function getProcesses() : ?array
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
     * @return Ups|null
     */
    public function getUps() : ?Ups
    {
        return $this->os->getUps();
    }

    /**
     * Printers
     *
     * @return Printer[]|null
     */
    public function getPrinters() : ?array
    {
        return $this->os->getPrinters();
    }


    /**
     * Samba status
     *
     * @return Samba|null
     */
    public function getSamba() : ?Samba
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
