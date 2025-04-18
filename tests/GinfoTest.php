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

        $ginfo = new Ginfo();
        $info = $ginfo->getInfo($customParser);
        $data = $info->getCustomParser($customParser::class);
        self::assertSame('OK', $data->getOk());
    }

    public function testPhp(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $php = $info->getPhp();

        self::assertSame('cli', $php->getSapiName());

        self::assertIsBool($php->getApcu()->isEnabled());
        self::assertIsBool($php->getOpcache()->isEnabled());
        self::assertIsBool($php->getFpm()->isEnabled());

        // \print_r($php);
    }

    public function testGeneral(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $general = $info->getGeneral();

        self::assertIsString($general->getOsName());

        // \print_r($general);
    }

    public function testCpu(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $cpu = $info->getCpu();
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
        $info = $ginfo->getInfo();
        $memory = $info->getMemory();
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
        $info = $ginfo->getInfo();
        $processes = $info->getProcesses();
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
        $info = $ginfo->getInfo();
        $network = $info->getNetwork();
        if (null === $network) {
            self::markTestSkipped('Can\'t get network');
        } else {
            self::assertNotEmpty($network[0]->getName());
            // \print_r($network);
        }
    }

    public function testUsb(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $usb = $info->getUsb();
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
        $info = $ginfo->getInfo();
        $pci = $info->getPci();
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
        $info = $ginfo->getInfo();
        $soundCard = $info->getSoundCard();
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
        $info = $ginfo->getInfo();
        $services = $info->getServices();
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
        $info = $ginfo->getInfo();
        $samba = $info->getSamba();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($samba);
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (!$samba) {
                self::markTestSkipped('Can\'t get samba');
            }
            // self::assertNotEmpty($samba->getServices()[0]->getService());
            // \print_r($samba);
        }
    }

    public function testUps(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $ups = $info->getUps();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($ups);
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (!$ups) {
                self::markTestSkipped('Can\'t get ups (need apcaccess)');
            } else {
                self::assertNotEmpty($ups->getName());
                // \print_r($ups);
            }
        }
    }

    public function testSelinux(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $selinux = $info->getSelinux();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertNull($selinux);
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (!$selinux) {
                self::markTestSkipped('Can\'t get selinux (need sestatus)');
            }
            // self::assertNotEmpty($selinux->getMode());
            // \print_r($selinux);
        }
    }

    public function testBattery(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $battery = $info->getBattery();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertEmpty($battery); // todo
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (!$battery) {
                self::markTestSkipped('Can\'t get battery info');
            } else {
                self::assertNotEmpty($battery[0]->getModel());
                // \print_r($battery);
            }
        }
    }

    public function testSensors(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $sensors = $info->getSensors();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertEmpty($sensors); // todo
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (!$sensors) {
                self::markTestSkipped('Can\'t get sensors (need hddtemp or mbmon or sensors or hwmon or thermal_zone or ipmitool or nvidia-smi or max_brightness)');
            } else {
                self::assertNotEmpty($sensors[0]->getName());
                // \print_r($sensors);
            }
        }
    }

    public function testPrinters(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $printers = $info->getPrinters();
        if ('Windows' === \PHP_OS_FAMILY) {
            self::assertEmpty($printers); // todo
            self::markTestSkipped('Not implemented for windows');
        } else {
            if (!$printers) {
                self::markTestSkipped('Can\'t get printers (need lpstat)');
            } else {
                self::assertNotEmpty($printers[0]->getName());
                // \print_r($printers);
            }
        }
    }

    public function testDisk(): void
    {
        $ginfo = new Ginfo();
        $info = $ginfo->getInfo();
        $disk = $info->getDisk();

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
