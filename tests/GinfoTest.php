<?php

namespace Ginfo\Tests;

use Ginfo\Ginfo;
use Ginfo\Info;
use PHPUnit\Framework\TestCase;

class GinfoTest extends TestCase
{
    /** @var Info */
    private $info;

    protected function setUp(): void
    {
        $ginfo = new Ginfo();
        $this->info = $ginfo->getInfo();
    }

    public function testPhp(): void
    {
        $php = $this->info->getPhp();

        self::assertSame('cli', $php->getSapiName());

        self::assertIsBool($php->getApcu()->isEnabled());
        self::assertIsBool($php->getOpcache()->isEnabled());
        self::assertIsBool($php->getFpm()->isEnabled());

        \print_r($php);
    }

    public function testGeneral(): void
    {
        $general = $this->info->getGeneral();

        self::assertIsString($general->getOsName());

        \print_r($general);
    }

    public function testCpu(): void
    {
        $cpu = $this->info->getCpu();
        if (null === $cpu) {
            self::markTestSkipped('Can\'t get cpu');
        } else {
            self::assertInstanceOf(Info\Cpu::class, $cpu);
            \print_r($cpu);
        }
    }

    public function testMemory(): void
    {
        $memory = $this->info->getMemory();
        if (null === $memory) {
            self::markTestSkipped('Can\'t get memory');
        } else {
            self::assertInstanceOf(Info\Memory::class, $memory);
            \print_r($memory);
        }
    }

    public function testProcesses(): void
    {
        $processes = $this->info->getProcesses();
        if (null === $processes) {
            self::markTestSkipped('Can\'t get processes');
        } else {
            self::assertNotEmpty($processes);
            \print_r($processes);
        }
    }

    public function testNetwork(): void
    {
        $network = $this->info->getNetwork();
        if (null === $network) {
            self::markTestSkipped('Can\'t get network');
        } else {
            self::assertNotEmpty($network);
            \print_r($network);
        }
    }

    public function testUsb(): void
    {
        $usb = $this->info->getUsb();
        if (null === $usb) {
            self::markTestSkipped('Can\'t get usb');
        } else {
            self::assertNotEmpty($usb);
            \print_r($usb);
        }
    }

    public function testPci(): void
    {
        $pci = $this->info->getPci();
        if (null === $pci) {
            self::markTestSkipped('Can\'t get pci');
        } else {
            self::assertNotEmpty($pci);
            \print_r($pci);
        }
    }

    public function testSoundCard(): void
    {
        $soundCard = $this->info->getSoundCard();
        if (null === $soundCard) {
            self::markTestSkipped('Can\'t get sound card');
        } else {
            self::assertNotEmpty($soundCard);
            \print_r($soundCard);
        }
    }

    public function testServices(): void
    {
        $services = $this->info->getServices();
        if (null === $services) {
            self::markTestSkipped('Can\'t get services (need systemd)');
        } else {
            self::assertNotEmpty($services);
            \print_r($services);
        }
    }

    public function testSamba(): void
    {
        $samba = $this->info->getSamba();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($samba);
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (null === $samba) {
                self::markTestSkipped('Can\'t get samba');
            } else {
                self::assertInstanceOf(Info\Samba::class, $samba);
                \print_r($samba);
            }
        }
    }

    public function testUps(): void
    {
        $ups = $this->info->getUps();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($ups);
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (null === $ups) {
                self::markTestSkipped('Can\'t get ups (need apcaccess)');
            } else {
                self::assertInstanceOf(Info\Ups::class, $ups);
                \print_r($ups);
            }
        }
    }

    public function testSelinux(): void
    {
        $selinux = $this->info->getSelinux();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($selinux);
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (null === $selinux) {
                self::markTestSkipped('Can\'t get selinux (need sestatus)');
            } else {
                self::assertInstanceOf(Info\Selinux::class, $selinux);
                \print_r($selinux);
            }
        }
    }

    public function testBattery(): void
    {
        $battery = $this->info->getBattery();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($battery); // todo
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (null === $battery) {
                self::markTestSkipped('Can\'t get battery info');
            } else {
                self::assertNotEmpty($battery);
                \print_r($battery);
            }
        }
    }

    public function testSensors(): void
    {
        $sensors = $this->info->getSensors();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($sensors); // todo
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (null === $sensors) {
                self::markTestSkipped('Can\'t get sensors (need hddtemp or mbmon or sensors or hwmon or thermal_zone or ipmitool or nvidia-smi or max_brightness)');
            } else {
                self::assertNotEmpty($sensors);
                \print_r($sensors);
            }
        }
    }

    public function testPrinters(): void
    {
        $printers = $this->info->getPrinters();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($printers); // todo
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (null === $printers) {
                self::markTestSkipped('Can\'t get printers (need lpstat)');
            } else {
                self::assertNotEmpty($printers);
                \print_r($printers);
            }
        }
    }

    public function testDisk(): void
    {
        $disk = $this->info->getDisk();

        $drivers = $disk->getDrives();
        $mounts = $disk->getMounts();
        $raids = $disk->getRaids();

        if (null === $drivers) {
            self::markTestSkipped('Can\'t get drivers');
        } else {
            self::assertNotEmpty($drivers);
            \print_r($drivers);
        }

        if (null === $mounts) {
            self::markTestSkipped('Can\'t get mounts');
        } else {
            self::assertNotEmpty($mounts);
            \print_r($mounts);
        }

        if (null === $raids) {
            self::markTestSkipped('Can\'t get raids');
        } else {
            self::assertNotEmpty($raids);
            \print_r($raids);
        }
    }
}
