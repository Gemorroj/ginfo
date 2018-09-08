<?php

namespace Ginfo\Tests;

use Ginfo\Ginfo;
use Ginfo\Info;

class GinfoTest extends \PHPUnit\Framework\TestCase
{
    /** @var Info */
    private $info;

    public function setUp()
    {
        $ginfo = new Ginfo();
        $this->info = $ginfo->getInfo();
    }

    public function testPhp()
    {
        $php = $this->info->getPhp();

        $this->assertSame('cli', $php->getSapiName());

        $this->assertInternalType('bool', $php->getApcu()->isEnabled());
        $this->assertInternalType('bool', $php->getOpcache()->isEnabled());

        \print_r($php);
    }

    public function testGeneral()
    {
        $general = $this->info->getGeneral();

        $this->assertInternalType('string', $general->getOsName());

        \print_r($general);
    }

    public function testCpu()
    {
        $cpu = $this->info->getCpu();
        $this->assertInstanceOf(Info\Cpu::class, $cpu);

        \print_r($cpu);
    }

    public function testMemory()
    {
        $memory = $this->info->getMemory();
        if (null === $memory) {
            $this->markTestSkipped('Can\'t get memory');
        } else {
            $this->assertInstanceOf(Info\Memory::class, $memory);
            \print_r($memory);
        }
    }

    public function testProcesses()
    {
        $processes = $this->info->getProcesses();
        $this->assertInternalType('array', $processes);

        \print_r($processes);
    }

    public function testNetwork()
    {
        $network = $this->info->getNetwork();
        $this->assertInternalType('array', $network);

        \print_r($network);
    }

    public function testUsb()
    {
        $usb = $this->info->getUsb();
        $this->assertInternalType('array', $usb);

        \print_r($usb);
    }

    public function testPci()
    {
        $pci = $this->info->getPci();
        $this->assertInternalType('array', $pci);

        \print_r($pci);
    }

    public function testSoundCard()
    {
        $soundCard = $this->info->getSoundCard();
        if (null === $soundCard) {
            $this->markTestSkipped('Can\'t get sound card');
        } else {
            $this->assertInternalType('array', $soundCard);
            \print_r($soundCard);
        }
    }

    public function testServices()
    {
        $services = $this->info->getServices();
        if (null === $services) {
            $this->markTestSkipped('Can\'t get services (need systemd)');
        } else {
            $this->assertInternalType('array', $services);
            \print_r($services);
        }
    }

    public function testSamba()
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

    public function testUps()
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

    public function testSelinux()
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

    public function testBattery()
    {
        $battery = $this->info->getBattery();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($battery); //todo
            $this->markTestSkipped('Not implemented for windows');
        } else {
            $this->assertInternalType('array', $battery);
        }

        \print_r($battery);
    }

    public function testSensors()
    {
        $sensors = $this->info->getSensors();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($sensors); //todo
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $sensors) {
                $this->markTestSkipped('Can\'t get sensors (need hddtemp or mbmon or sensors or hwmon or thermal_zone or ipmitool or nvidia-smi or max_brightness)');
            } else {
                $this->assertInternalType('array', $sensors);
                \print_r($sensors);
            }
        }
    }

    public function testPrinters()
    {
        $printers = $this->info->getPrinters();
        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($printers); //todo
            $this->markTestSkipped('Not implemented for windows');
        } else {
            if (null === $printers) {
                $this->markTestSkipped('Can\'t get printers (need lpstat)');
            } else {
                $this->assertInternalType('array', $printers);
                \print_r($printers);
            }
        }
    }

    public function testDisk()
    {
        $disk = $this->info->getDisk();
        $this->assertInternalType('array', $disk->getDrives());
        $this->assertInternalType('array', $disk->getMounts());

        if (\DIRECTORY_SEPARATOR === '\\') {
            $this->assertNull($disk->getRaids()); //todo
            //$this->markTestSkipped('Not implemented for windows');
        } else {
            $this->assertInternalType('array', $disk->getRaids());
        }

        \print_r($disk);
    }
}
