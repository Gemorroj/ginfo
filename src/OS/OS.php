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
    public function getArchitecture(): string
    {
        return \php_uname('m');
    }

    /**
     * @return string the OS kernel. A few OS classes override this.
     */
    public function getKernel(): string
    {
        return \php_uname('r');
    }

    /**
     * @return string the OS' hostname A few OS classes override this
     */
    public function getHostName(): string
    {
        return \php_uname('n');
    }

    /**
     * @return string the OS' name
     */
    abstract public function getOsName(): string;

    /**
     * @return float|null seconds
     */
    abstract public function getUptime(): ?float;

    abstract public function getVirtualization(): ?string;

    abstract public function getCpu(): ?Cpu;

    /**
     * @return float[]|null
     */
    abstract public function getLoad(): ?array;

    abstract public function getMemory(): ?Memory;

    /**
     * @return SoundCard[]|null
     */
    abstract public function getSoundCards(): ?array;

    /**
     * @return string[]|null
     */
    abstract public function getLoggedUsers(): ?array;

    /**
     * Get brand/name of motherboard/server.
     */
    abstract public function getModel(): ?string;

    /**
     * @return Usb[]|null
     */
    abstract public function getUsb(): ?array;

    /**
     * @return Pci[]|null
     */
    abstract public function getPci(): ?array;

    /**
     * @return Network[]|null
     */
    abstract public function getNetwork(): ?array;

    /**
     * @return Drive[]|null
     */
    abstract public function getDrives(): ?array;

    /**
     * @return Mount[]|null
     */
    abstract public function getMounts(): ?array;

    /**
     * @return Raid[]|null
     */
    abstract public function getRaids(): ?array;

    /**
     * @return Battery[]|null
     */
    abstract public function getBattery(): ?array;

    /**
     * @return Sensor[]|null
     */
    abstract public function getSensors(): ?array;

    /**
     * @return Process[]|null
     */
    abstract public function getProcesses(): ?array;

    /**
     * @return Service[]|null
     */
    abstract public function getServices(): ?array;

    abstract public function getUps(): ?Ups;

    /**
     * @return Printer[]|null
     */
    abstract public function getPrinters(): ?array;

    abstract public function getSamba(): ?Samba;

    abstract public function getSelinux(): ?Selinux;
}
