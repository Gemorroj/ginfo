<?php

namespace Ginfo\OS;

use Ginfo\Info\Battery;
use Ginfo\Info\Cpu;
use Ginfo\Info\Disk\Drive;
use Ginfo\Info\Disk\Mount;
use Ginfo\Info\Disk\Raid;
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
     * @return Cpu|null
     */
    public abstract function getCpu() : ?Cpu;

    /**
     * @return float[]|null
     */
    public abstract function getLoad() : ?array;

    /**
     * @return Memory|null
     */
    public abstract function getMemory() : ?Memory;

    /**
     * @return SoundCard[]|null
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
     * @return Usb[]|null
     */
    public abstract function getUsb() : ?array;

    /**
     * @return Pci[]|null
     */
    public abstract function getPci() : ?array;

    /**
     * @return Network[]|null
     */
    public abstract function getNetwork() : ?array;

    /**
     * @return Drive[]|null
     */
    public abstract function getDrives() : ?array;

    /**
     * @return Mount[]|null
     */
    public abstract function getMounts() : ?array;

    /**
     * @return Raid[]|null
     */
    public abstract function getRaids() : ?array;

    /**
     * @return Battery[]|null
     */
    public abstract function getBattery() : ?array;

    /**
     * @return Sensor[]|null
     */
    public abstract function getSensors() : ?array;

    /**
     * @return Process[]|null
     */
    public abstract function getProcesses() : ?array;

    /**
     * @return Service[]|null
     */
    public abstract function getServices() : ?array;

    /**
     * @return Ups|null
     */
    public abstract function getUps() : ?Ups;

    /**
     * @return Printer[]|null
     */
    public abstract function getPrinters() : ?array;

    /**
     * @return Samba|null
     */
    public abstract function getSamba() : ?Samba;

    /**
     * @return Selinux|null
     */
    public abstract function getSelinux() : ?Selinux;
}
