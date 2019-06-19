<?php

namespace Ginfo\Tests;

use Ginfo\Ginfo;
use Ginfo\Info;
use PHPUnit\Framework\TestCase;

class GinfoTest extends TestCase
{
    /** @var Info */
    private $info;

    public function setUp()
    {
        $ginfo = new Ginfo();
        $this->info = $ginfo->getInfo();
    }

    public function testPhp(): void
    {
        $php = $this->info->getPhp();

        $this->assertSame('cli', $php->getSapiName());

        $this->assertIsBool($php->getApcu()->isEnabled());
        $this->assertIsBool($php->getOpcache()->isEnabled());
        $this->assertIsBool($php->getFpm()->isEnabled());

        \print_r($php);
    }

    public function testGeneral(): void
    {
        $general = $this->info->getGeneral();

        $this->assertIsString($general->getOsName());

        \print_r($general);
    }

    public function testCpu(): void
    {
        $cpu = $this->info->getCpu();
        if (null === $cpu) {
            $this->markTestSkipped('Can\'t get cpu');
        } else {
            $this->assertInstanceOf(Info\Cpu::class, $cpu);
            \print_r($cpu);
        }
    }

    public function testMemory(): void
    {
        $memory = $this->info->getMemory();
        if (null === $memory) {
            $this->markTestSkipped('Can\'t get memory');
        } else {
            $this->assertInstanceOf(Info\Memory::class, $memory);
            \print_r($memory);
        }
    }

    public function testProcesses(): void
    {
        $processes = $this->info->getProcesses();
        if (null === $processes) {
            $this->markTestSkipped('Can\'t get processes');
        } else {
            $this->assertIsArray($processes);
            \print_r($processes);
        }
    }

    public function testNetwork(): void
    {
        $network = $this->info->getNetwork();
        if (null === $network) {
            $this->markTestSkipped('Can\'t get network');
        } else {
            $this->assertIsArray($network);
            \print_r($network);
        }
    }

    public function testUsb(): void
    {
        $usb = $this->info->getUsb();
        if (null === $usb) {
            $this->markTestSkipped('Can\'t get usb');
        } else {
            $this->assertIsArray($usb);
            \print_r($usb);
        }
    }

    public function testPci(): void
    {
        $pci = $this->info->getPci();
        if (null === $pci) {
            $this->markTestSkipped('Can\'t get pci');
        } else {
            $this->assertIsArray($pci);
            \print_r($pci);
        }
    }

    public function testSoundCard(): void
    {
        $soundCard = $this->info->getSoundCard();
        if (null === $soundCard) {
            $this->markTestSkipped('Can\'t get sound card');
        } else {
            $this->assertIsArray($soundCard);
            \print_r($soundCard);
        }
    }

    public function testServices(): void
    {
        $services = $this->info->getServices();
        if (null === $services) {
            $this->markTestSkipped('Can\'t get services (need systemd)');
        } else {
            $this->assertIsArray($services);
            \print_r($services);
        }
    }

    public function testSamba(): void
    {
        $samba = $this->info->getSamba();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($samba);
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $samba) {
                $this->markTestSkipped('Can\'t get samba');
            } else {
                $this->assertInstanceOf(Info\Samba::class, $samba);
                \print_r($samba);
            }
        }
    }

    public function testUps(): void
    {
        $ups = $this->info->getUps();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($ups);
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $ups) {
                $this->markTestSkipped('Can\'t get ups (need apcaccess)');
            } else {
                $this->assertInstanceOf(Info\Ups::class, $ups);
                \print_r($ups);
            }
        }
    }

    public function testSelinux(): void
    {
        $selinux = $this->info->getSelinux();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($selinux);
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $selinux) {
                $this->markTestSkipped('Can\'t get selinux (need sestatus)');
            } else {
                $this->assertInstanceOf(Info\Selinux::class, $selinux);
                \print_r($selinux);
            }
        }
    }

    public function testBattery(): void
    {
        $battery = $this->info->getBattery();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($battery); //todo
            $this->markTestSkipped('Not implemented for windows');
        } else {
            $this->assertIsArray($battery);
        }

        \print_r($battery);
    }

    public function testSensors(): void
    {
        $sensors = $this->info->getSensors();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($sensors); //todo
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $sensors) {
                $this->markTestSkipped('Can\'t get sensors (need hddtemp or mbmon or sensors or hwmon or thermal_zone or ipmitool or nvidia-smi or max_brightness)');
            } else {
                $this->assertIsArray($sensors);
                \print_r($sensors);
            }
        }
    }

    public function testPrinters(): void
    {
        $printers = $this->info->getPrinters();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($printers); //todo
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $printers) {
                $this->markTestSkipped('Can\'t get printers (need lpstat)');
            } else {
                $this->assertIsArray($printers);
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
            $this->markTestSkipped('Can\'t get drivers');
        } else {
            $this->assertIsArray($drivers);
            \print_r($drivers);
        }

        if (null === $mounts) {
            $this->markTestSkipped('Can\'t get mounts');
        } else {
            $this->assertIsArray($mounts);
            \print_r($mounts);
        }

        if (null === $raids) {
            $this->markTestSkipped('Can\'t get raids');
        } else {
            $this->assertIsArray($raids);
            \print_r($raids);
        }
    }
}
