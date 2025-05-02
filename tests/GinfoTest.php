<?php

namespace Ginfo\Tests;

use Ginfo\Ginfo;
use Ginfo\Info\InfoInterface;
use Ginfo\InfoParserInterface;
use PHPUnit\Framework\TestCase;

final class GinfoTest extends TestCase
{
    public function testGetCustomParser(): void
    {
        $customParser = new class implements InfoParserInterface {
            public function run(): ?InfoInterface
            {
                return new class implements InfoInterface {
                    public function getOk(): string
                    {
                        return 'OK';
                    }
                };
            }
        };

        $ginfo = new Ginfo(customParsers: [$customParser]);
        $data = $ginfo->getCustomParser($customParser::class);
        self::assertSame('OK', $data->getOk());
    }

    public function testPhp(): void
    {
        $ginfo = new Ginfo();
        $php = $ginfo->getPhp();

        self::assertSame('cli', $php->getSapiName());

        self::assertIsBool($php->getApcu()->isEnabled());
        self::assertIsBool($php->getOpcache()->isEnabled());
        self::assertIsBool($php->getFpm()->isEnabled());

        // \print_r($php);
    }

    public function testGeneral(): void
    {
        $ginfo = new Ginfo();
        $general = $ginfo->getGeneral();

        self::assertIsString($general->getOsName());
        self::assertCount(3, $general->getLoad());

        // \print_r($general);
    }

    public function testCpu(): void
    {
        $ginfo = new Ginfo();
        $cpu = $ginfo->getCpu();
        if (!$cpu) {
            self::markTestSkipped('Can\'t get cpu');
        } else {
            self::assertNotEmpty($cpu->getProcessors());
            // \print_r($cpu);
        }
    }

    public function testMemory(): void
    {
        $ginfo = new Ginfo();
        $memory = $ginfo->getMemory();
        if (!$memory) {
            self::markTestSkipped('Can\'t get memory');
        } else {
            self::assertGreaterThan(1, $memory->getTotal());
            // \print_r($memory);
        }
    }

    public function testProcesses(): void
    {
        $ginfo = new Ginfo();
        $processes = $ginfo->getProcesses();
        if (!$processes) {
            self::markTestSkipped('Can\'t get processes');
        } else {
            self::assertNotEmpty($processes[0]->getName());
            // \print_r($processes);
        }
    }

    public function testNetwork(): void
    {
        $ginfo = new Ginfo();
        $network = $ginfo->getNetwork();
        if (!$network) {
            self::markTestSkipped('Can\'t get network');
        } else {
            self::assertNotEmpty($network[0]->getName());
            // \print_r($network);
        }
    }

    public function testUsb(): void
    {
        $ginfo = new Ginfo();
        $usb = $ginfo->getUsb();
        if (!$usb) {
            self::markTestSkipped('Can\'t get usb');
        } else {
            self::assertNotEmpty($usb[0]->getVendor());
            // \print_r($usb);
        }
    }

    public function testPci(): void
    {
        $ginfo = new Ginfo();
        $pci = $ginfo->getPci();
        if (!$pci) {
            self::markTestSkipped('Can\'t get pci');
        } else {
            self::assertNotEmpty($pci[0]->getVendor());
            // \print_r($pci);
        }
    }

    public function testSoundCard(): void
    {
        $ginfo = new Ginfo();
        $soundCard = $ginfo->getSoundCard();
        if (!$soundCard) {
            self::markTestSkipped('Can\'t get sound card');
        } else {
            self::assertNotEmpty($soundCard[0]->getName());
            // \print_r($soundCard);
        }
    }

    public function testServices(): void
    {
        $ginfo = new Ginfo();
        $services = $ginfo->getServices();
        if (!$services) {
            self::markTestSkipped('Can\'t get services (need systemd)');
        } else {
            self::assertNotEmpty($services[0]->getName());
            // \print_r($services);
        }
    }

    public function testSamba(): void
    {
        $ginfo = new Ginfo();
        $samba = $ginfo->getSamba();
        if (!$samba) {
            self::markTestSkipped('Can\'t get samba');
        }
        // self::assertNotEmpty($samba->getServices()[0]->getService());
        // \print_r($samba);
    }

    public function testUps(): void
    {
        $ginfo = new Ginfo();
        $ups = $ginfo->getUps();
        if (!$ups) {
            self::markTestSkipped('Can\'t get ups (need apcaccess)');
        } else {
            self::assertNotEmpty($ups->getName());
            // \print_r($ups);
        }
    }

    public function testSelinux(): void
    {
        $ginfo = new Ginfo();
        $selinux = $ginfo->getSelinux();
        if (!$selinux) {
            self::markTestSkipped('Can\'t get selinux (need sestatus)');
        }
        // self::assertNotEmpty($selinux->getMode());
        // \print_r($selinux);
    }

    public function testBattery(): void
    {
        $ginfo = new Ginfo();
        $battery = $ginfo->getBattery();
        if (!$battery) {
            self::markTestSkipped('Can\'t get battery info');
        } else {
            self::assertNotEmpty($battery[0]->getModel());
            // \print_r($battery);
        }
    }

    public function testSensors(): void
    {
        $ginfo = new Ginfo();
        $sensors = $ginfo->getSensors();
        if (!$sensors) {
            self::markTestSkipped('Can\'t get sensors (need hddtemp or mbmon or sensors or hwmon or thermal_zone or ipmitool or nvidia-smi or max_brightness)');
        } else {
            self::assertNotEmpty($sensors[0]->getName());
            // \print_r($sensors);
        }
    }

    public function testPrinters(): void
    {
        $ginfo = new Ginfo();
        $printers = $ginfo->getPrinters();
        if (!$printers) {
            self::markTestSkipped('Can\'t get printers (need lpstat)');
        } else {
            self::assertNotEmpty($printers[0]->getName());
            // \print_r($printers);
        }
    }

    public function testDisk(): void
    {
        $ginfo = new Ginfo();
        $disk = $ginfo->getDisk();

        $drivers = $disk->getDrives();
        $mounts = $disk->getMounts();
        $raids = $disk->getRaids();

        if (!$drivers) {
            self::markTestSkipped('Can\'t get drivers');
        } else {
            self::assertNotEmpty($drivers[0]->getName());
            // \print_r($drivers);
        }

        if (!$mounts) {
            self::markTestSkipped('Can\'t get mounts');
        } else {
            self::assertNotEmpty($mounts[0]->getDevice());
            // \print_r($mounts);
        }

        if (!$raids) {
            self::markTestSkipped('Can\'t get raids');
        } else {
            self::assertNotEmpty($raids[0]->getDevice());
            // \print_r($raids);
        }
    }
}
