<?php

namespace Ginfo\Os;

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

interface OsInterface
{
    /**
     * @return string the arch OS
     */
    public function getArchitecture(): string;

    /**
     * @return string the OS kernel. A few OS classes override this.
     */
    public function getKernel(): string;

    /**
     * @return string the OS' hostname A few OS classes override this
     */
    public function getHostName(): string;

    /**
     * @return string the OS' name
     */
    public function getOsName(): string;

    /**
     * @return int|null seconds
     */
    public function getUptime(): ?int;

    public function getVirtualization(): ?string;

    public function getCpu(): ?Cpu;

    /**
     * @return float[]|null
     */
    public function getLoad(): ?array;

    public function getMemory(): ?Memory;

    /**
     * @return SoundCard[]|null
     */
    public function getSoundCards(): ?array;

    /**
     * @return string[]|null
     */
    public function getLoggedUsers(): ?array;

    /**
     * Get brand/name of motherboard/server.
     */
    public function getModel(): ?string;

    /**
     * @return Usb[]|null
     */
    public function getUsb(): ?array;

    /**
     * @return Pci[]|null
     */
    public function getPci(): ?array;

    /**
     * @return Network[]|null
     */
    public function getNetwork(): ?array;

    /**
     * @return Drive[]|null
     */
    public function getDrives(): ?array;

    /**
     * @return Mount[]|null
     */
    public function getMounts(): ?array;

    /**
     * @return Raid[]|null
     */
    public function getRaids(): ?array;

    /**
     * @return Battery[]|null
     */
    public function getBattery(): ?array;

    /**
     * @return Sensor[]|null
     */
    public function getSensors(): ?array;

    /**
     * @return Process[]|null
     */
    public function getProcesses(): ?array;

    /**
     * @return Service[]|null
     */
    public function getServices(): ?array;

    public function getUps(): ?Ups;

    /**
     * @return Printer[]|null
     */
    public function getPrinters(): ?array;

    public function getSamba(): ?Samba;

    public function getSelinux(): ?Selinux;
}
