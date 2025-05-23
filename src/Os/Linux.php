<?php

namespace Ginfo\Os;

use Ginfo\CommonTrait;
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
use Ginfo\Parser\Apcaccess;
use Ginfo\Parser\Hwpci;
use Ginfo\Parser\Lpstat;
use Ginfo\Parser\Mdadm;
use Ginfo\Parser\ProcCpuinfo;
use Ginfo\Parser\ProcMeminfo;
use Ginfo\Parser\Sensor\Hddtemp;
use Ginfo\Parser\Sensor\Hwmon;
use Ginfo\Parser\Sensor\Ipmi;
use Ginfo\Parser\Sensor\Mbmon;
use Ginfo\Parser\Sensor\Nvidia;
use Ginfo\Parser\Sensor\Sensors;
use Ginfo\Parser\Sensor\ThermalZone;
use Ginfo\Parser\Sestatus;
use Ginfo\Parser\Smbstatus;
use Ginfo\Parser\Systemd;
use Ginfo\Parser\Who;

class Linux implements OsInterface
{
    use CommonTrait;

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

    public function getMemory(): ?Memory
    {
        $memInfo = (new ProcMeminfo())->run();
        if (!$memInfo) {
            return null;
        }

        return new Memory(
            $memInfo['total'],
            $memInfo['used'],
            $memInfo['free'],
            $memInfo['available'],
            $memInfo['shared'],
            $memInfo['buffers'],
            $memInfo['cached'],
            $memInfo['swapTotal'],
            $memInfo['swapUsed'],
            $memInfo['swapFree']
        );
    }

    public function getCpu(): ?Cpu
    {
        $cpuInfo = (new ProcCpuinfo())->run();
        if (!$cpuInfo) {
            return null;
        }

        $processors = [];
        foreach ($cpuInfo['processors'] as $processor) {
            $processors[] = new Cpu\Processor(
                $processor['model'],
                $processor['speed'],
                $processor['l2Cache'],
                $processor['flags'],
                $processor['architecture'],
            );
        }

        return new Cpu($cpuInfo['physical'], $cpuInfo['cores'], $cpuInfo['virtual'], $cpuInfo['hyperThreading'], $processors);
    }

    public function getUptime(): ?int
    {
        $uptime = self::getContents('/proc/uptime');
        if (null === $uptime) {
            return null;
        }

        return (int) \explode(' ', $uptime, 2)[0];
    }

    public function getDrives(): ?array
    {
        $partitions = [];
        $partitionsContents = self::getContents('/proc/partitions');
        if (null === $partitionsContents) {
            return null;
        }

        if (\preg_match_all('/(\d+)\s+([a-z]{3}|nvme\d+n\d+|[a-z]+\d+)(p?\d+)$/m', $partitionsContents, $partitionsMatch, \PREG_SET_ORDER) > 0) {
            foreach ($partitionsMatch as $partition) {
                $partitions[$partition[2]][] = new Drive\Partition(
                    $partition[1] * 1024,
                    $partition[2].$partition[3]
                );
            }
        }

        $paths = \glob('/sys/block/*/device/uevent', \GLOB_NOSORT);
        if (!$paths) {
            return null;
        }

        $drives = [];
        foreach ($paths as $path) {
            $parts = \explode('/', $path);

            // Attempt getting read/write stats
            if (1 !== \preg_match('/^(\d+)\s+\d+\s+\d+\s+\d+\s+(\d+)/', self::getContents(\dirname($path, 2).'/stat'), $statMatches)) {
                $reads = null;
                $writes = null;
            } else {
                [, $reads, $writes] = $statMatches;
            }

            $type = '';
            if ('0' === self::getContents(\dirname($path, 2).'/queue/rotational')) {
                if ('SD' === self::getContents(\dirname($path).'/type')) {
                    $type = ' (SD)';
                } else {
                    $type = ' (SSD)';
                }
            }

            $namePartition = $parts[3];
            $p = \array_key_exists($namePartition, $partitions) ? $partitions[$namePartition] : [];

            $drives[] = new Drive(
                self::getContents(\dirname($path).'/model', 'Unknown').$type,
                '/dev/'.$namePartition,
                self::getContents(\dirname($path, 2).'/size', '0') * 512,
                self::getContents(\dirname($path).'/vendor'),
                $reads,
                $writes,
                $p,
            );
        }

        \usort($drives, static function (Drive $a, Drive $b): int {
            return $a->getName() <=> $b->getName();
        });

        return $drives;
    }

    public function getMounts(): ?array
    {
        $contents = self::getContents('/proc/mounts');
        if (null === $contents) {
            return null;
        }

        if (false === \preg_match_all('/^(\S+) (\S+) (\S+) (.+) \d \d$/m', $contents, $match, \PREG_SET_ORDER)) {
            return null;
        }

        $mounts = [];
        foreach ($match as $mount) {
            // Spaces and other things in the mount path are escaped C style. Fix that.
            $mount[2] = \stripcslashes($mount[2]);

            if (\is_readable($mount[2])) {
                $size = \disk_total_space($mount[2]);
                $size = false === $size ? null : $size;

                $free = \disk_free_space($mount[2]);
                $free = false === $free ? null : $free;

                $used = (null !== $size && null !== $free) ? $size - $free : null;
            } else {
                $size = $free = $used = null;
            }

            $mounts[] = new Mount(
                $mount[1],
                $mount[2],
                $mount[3],
                $size,
                $used,
                $free,
                null !== $size && null !== $free && $size > 0 ? \round($free / $size, 2) * 100 : null,
                null !== $size && null !== $used && $size > 0 ? \round($used / $size, 2) * 100 : null,
                \explode(',', $mount[4])
            );
        }

        \usort($mounts, static function (Mount $a, Mount $b): int {
            return $a->getMount() <=> $b->getMount();
        });

        return $mounts;
    }

    public function getRaids(): ?array
    {
        $data = (new Mdadm())->run();
        if (null === $data) {
            return null;
        }

        $raids = [];
        foreach ($data as $raid) {
            $drives = [];
            foreach ($raid['drives'] as $drive) {
                $drives[] = new Raid\Drive($drive['path'], $drive['state']);
            }

            $raids[] = new Raid(
                $raid['device'],
                $raid['status'],
                $raid['level'],
                $raid['size'],
                $raid['count']['active'],
                $raid['count']['total'],
                $raid['chart'],
                $drives,
            );
        }

        \usort($raids, static function (Raid $a, Raid $b): int {
            return $a->getDevice() <=> $b->getDevice();
        });

        return $raids;
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getSensors(?string $cwd = null): ?array
    {
        $return = [];

        $hddtempRes = (new Hddtemp())->run();
        if ($hddtempRes) {
            $return = \array_merge($return, $hddtempRes);
        }

        $mbmonRes = (new Mbmon())->run();
        if ($mbmonRes) {
            $return = \array_merge($return, $mbmonRes);
        }

        $sensorsRes = (new Sensors())->run($cwd);
        if ($sensorsRes) {
            $return = \array_merge($return, $sensorsRes);
        }

        $hwmonRes = (new Hwmon())->run();
        if ($hwmonRes) {
            $return = \array_merge($return, $hwmonRes);
        }

        $thermalZoneRes = (new ThermalZone())->run();
        if ($thermalZoneRes) {
            $return = \array_merge($return, $thermalZoneRes);
        }

        $ipmi = (new Ipmi())->run($cwd);
        if ($ipmi) {
            $return = \array_merge($return, $ipmi);
        }

        $nvidia = (new Nvidia())->run($cwd);
        if ($nvidia) {
            $return = \array_merge($return, $nvidia);
        }

        // Laptop backlight percentage
        $paths = \glob('/sys/{devices/virtual,class}/backlight/*/max_brightness', \GLOB_NOSORT | \GLOB_BRACE);
        if ($paths) {
            foreach ($paths as $bl) {
                $max = self::getContents($bl);
                $cur = self::getContents(\dirname($bl).'/actual_brightness');
                if (null === $cur) {
                    continue;
                }

                if ($max < 0 || $cur < 0) {
                    continue;
                }
                $return[] = [
                    'name' => 'Backlight brightness',
                    'value' => \round($cur / $max, 2) * 100,
                    'unit' => '%',
                    'path' => null,
                ];
            }
        }

        $out = [];
        foreach ($return as $v) {
            $out[] = new Sensor($v['name'], $v['value'], $v['unit'], $v['path']);
        }

        return $out ?: null;
    }

    public function getUsb(): ?array
    {
        $data = (new Hwpci())->run(Hwpci::MODE_USB);
        if (null === $data) {
            return null;
        }

        $out = [];
        foreach ($data as $v) {
            $out[] = new Usb($v['vendor'], $v['device'], $v['speed']);
        }

        return $out;
    }

    public function getPci(): ?array
    {
        $data = (new Hwpci())->run(Hwpci::MODE_PCI);
        if (null === $data) {
            return null;
        }

        $out = [];
        foreach ($data as $v) {
            $out[] = new Pci($v['vendor'], $v['device']);
        }

        return $out;
    }

    public function getLoad(): ?array
    {
        $load = self::getContents('/proc/loadavg');
        if (!$load) {
            return null;
        }

        $loadArr = \explode(' ', $load);

        return [
            (float) $loadArr[0],
            (float) $loadArr[1],
            (float) $loadArr[2],
        ];
    }

    public function getNetwork(): ?array
    {
        $paths = \glob('/sys/class/net/*', \GLOB_NOSORT);
        if (!$paths) {
            return null;
        }

        $return = [];
        foreach ($paths as $path) {
            $speed = (int) self::getContents($path.'/speed'); // Mbits/sec
            if ($speed) {
                $speed *= 1000000;
            }

            $operstateContents = self::getContents($path.'/operstate');
            $state = \in_array($operstateContents, ['up', 'down'], true) ? $operstateContents : null;

            if (null === $state && \file_exists($path.'/carrier')) {
                $state = self::getContents($path.'/carrier') ? 'up' : 'down';
            }

            // Try the weird ways of getting type (https://stackoverflow.com/a/16060638)
            $typeCode = self::getContents($path.'/type');

            if ('772' === $typeCode) {
                $type = 'Loopback';
            } elseif ('65534' === $typeCode) {
                $type = 'Tunnel';
            } elseif ('776' === $typeCode) {
                $type = 'IPv6 in IPv4';
            } else {
                $typeContents = \mb_strtoupper(self::getContents($path.'/device/modalias', ''));
                [$typeMatch] = \explode(':', $typeContents, 2);

                $ueventContents = @\parse_ini_file($path.'/uevent');

                if ($ueventContents && isset($ueventContents['DEVTYPE'])) {
                    $type = \ucfirst($ueventContents['DEVTYPE']);
                    if (\in_array($typeMatch, ['PCI', 'USB'], true)) {
                        $type .= ' ('.$typeMatch.')';
                    }
                    $deviceUeventContents = @\parse_ini_file($path.'/device/uevent');
                    if ($deviceUeventContents && isset($deviceUeventContents['DRIVER'])) {
                        $type .= ' ('.$deviceUeventContents['DRIVER'].')';
                    }
                } elseif (\in_array($typeMatch, ['PCI', 'USB'], true)) {
                    $type = 'Ethernet ('.$typeMatch.')';

                    $deviceUeventContents = @\parse_ini_file($path.'/device/uevent');
                    if ($deviceUeventContents && isset($deviceUeventContents['DRIVER'])) {
                        $type .= ' ('.$deviceUeventContents['DRIVER'].')';
                    }
                } elseif ('VIRTIO' === $typeMatch) {
                    $type = 'VirtIO';
                } elseif ('XEN:VIF' === $typeContents) {
                    $type = 'Xen (VIF)';
                } elseif ('XEN-BACKEND:VIF' === $typeContents) {
                    $type = 'Xen Backend (VIF)';
                } elseif (\is_dir($path.'/bridge')) {
                    $type = 'Bridge';
                } elseif (\is_dir($path.'/bonding')) {
                    $type = 'Bond';
                } else {
                    $type = null;
                }

                // TODO find some way of finding out what provides the virt-specific kvm vnet devices
            }

            $statsReceived = new Network\Stats(
                (int) self::getContents($path.'/statistics/rx_bytes', '0'),
                (int) self::getContents($path.'/statistics/rx_errors', '0'),
                (int) self::getContents($path.'/statistics/rx_packets', '0')
            );
            $statsSent = new Network\Stats(
                (int) self::getContents($path.'/statistics/tx_bytes', '0'),
                (int) self::getContents($path.'/statistics/tx_errors', '0'),
                (int) self::getContents($path.'/statistics/tx_packets', '0')
            );

            $return[] = new Network(
                \basename($path),
                $speed,
                $type,
                $state,
                $statsReceived,
                $statsSent,
            );
        }

        return $return;
    }

    public function getBattery(): ?array
    {
        $paths = \glob('/sys/class/power_supply/BAT*', \GLOB_NOSORT);
        if (!$paths) {
            return null;
        }

        $return = [];
        foreach ($paths as $b) {
            $uevent = self::getContents($b.'/uevent');
            if (null === $uevent) {
                continue;
            }

            $block = self::parseKeyValueBlock($uevent, '=');

            $return[] = new Battery(
                $block['POWER_SUPPLY_MODEL_NAME'],
                $block['POWER_SUPPLY_MANUFACTURER'],
                $block['POWER_SUPPLY_STATUS'],
                $block['POWER_SUPPLY_CAPACITY'],
                $block['POWER_SUPPLY_VOLTAGE_NOW'],
                $block['POWER_SUPPLY_TECHNOLOGY'] ?? null,
                $block['POWER_SUPPLY_ENERGY_NOW'] ?? null,
                $block['POWER_SUPPLY_ENERGY_FULL'] ?? null,
                $block['POWER_SUPPLY_CHARGE_NOW'] ?? null,
                $block['POWER_SUPPLY_CHARGE_FULL'] ?? null
            );
        }

        return $return;
    }

    public function getSoundCards(): ?array
    {
        $lines = @\file('/proc/asound/cards', \FILE_SKIP_EMPTY_LINES);
        if (false === $lines) {
            return null;
        }

        $cards = [];
        for ($i = 0, $l = \count($lines); $i < $l; $i += 2) {
            $name = \trim(\explode(']:', $lines[$i], 2)[1]);
            $vendor = \trim(\explode(' at ', $lines[$i + 1], 2)[0]);

            $cards[] = new SoundCard($vendor, $name);
        }

        return $cards;
    }

    public function getProcesses(): ?array
    {
        $processes = \glob('/proc/*/status', \GLOB_NOSORT);
        if (!$processes) {
            return null;
        }

        $result = [];
        foreach ($processes as $process) {
            $statusContents = self::getContents($process);
            if (null === $statusContents) {
                continue;
            }

            $cmdlineContents = self::getContents(\dirname($process).'/cmdline');
            $ioContents = self::getContents(\dirname($process).'/io');

            $blockIo = $ioContents ? self::parseKeyValueBlock($ioContents) : null;
            $blockStatus = self::parseKeyValueBlock($statusContents);

            $uid = \explode("\t", $blockStatus['Uid'], 2)[0];
            $user = \posix_getpwuid($uid);

            if (isset($blockStatus['VmSize'])) {
                $vmSize = (float) $blockStatus['VmSize']; // drop kB
                $vmSize *= 1024;
            } else {
                $vmSize = null;
            }

            if (isset($blockStatus['VmSize'])) {
                $vmPeak = (float) $blockStatus['VmPeak']; // drop kB
                $vmPeak *= 1024;
            } else {
                $vmPeak = null;
            }

            $result[] = new Process(
                $blockStatus['Name'],
                $blockStatus['Pid'],
                null !== $cmdlineContents ? \str_replace("\0", ' ', $cmdlineContents) : null,
                $blockStatus['Threads'],
                $blockStatus['State'],
                $vmSize,
                $vmPeak,
                $user ? $user['name'] : $uid,
                $blockIo['read_bytes'] ?? null,
                $blockIo['write_bytes'] ?? null
            );
        }

        return $result;
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getServices(?string $cwd = null): ?array
    {
        $systemd = new Systemd();
        $services = $systemd->run(Service::TYPE_SERVICE, $cwd);
        $targets = $systemd->run(Service::TYPE_TARGET, $cwd);
        if (null === $services && null === $targets) {
            return null;
        }

        $out = [];
        if ($services) {
            foreach ($services as $service) {
                $out[] = new Service($service['name'], $service['description'], $service['loaded'], $service['started'], $service['state'], Service::TYPE_SERVICE);
            }
        }
        if ($targets) {
            foreach ($targets as $service) {
                $out[] = new Service($service['name'], $service['description'], $service['loaded'], $service['started'], $service['state'], Service::TYPE_TARGET);
            }
        }

        return $out;
    }

    public function getOsName(): string
    {
        $stringReleases = [
            '/etc/centos-release',
            '/etc/fedora-release',
            '/etc/oracle-release',
            '/etc/redhat-release',
            '/etc/system-release',
            '/etc/gentoo-release',
            '/etc/alpine-release',
            '/etc/slackware-version',
        ];

        foreach ($stringReleases as $releaseFile) {
            $os = self::getContents($releaseFile);
            if (null !== $os) {
                return $os;
            }
        }

        $lsbRelease = @\parse_ini_file('/etc/lsb-release');
        if ($lsbRelease) {
            return $lsbRelease['DISTRIB_DESCRIPTION'];
        }

        $suseRelease = @\file('/etc/SuSE-release', \FILE_SKIP_EMPTY_LINES);
        if ($suseRelease) {
            return $suseRelease[0];
        }

        $debianVersion = self::getContents('/etc/debian_version');
        if (null !== $debianVersion) {
            return 'Debian '.$debianVersion;
        }

        return \PHP_OS;
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getLoggedUsers(?string $cwd = null): ?array
    {
        return (new Who())->run($cwd);
    }

    public function getVirtualization(): ?string
    {
        if (\is_file('/proc/vz/veinfo')) {
            return 'OpenVZ';
        }

        $biosVendor = self::getContents('/sys/devices/virtual/dmi/id/bios_vendor');
        if ('Veertu' === $biosVendor) {
            return 'Veertu';
        }
        if (\str_starts_with($biosVendor, 'Parallels')) {
            return 'Parallels';
        }

        if (\str_contains(self::getContents('/proc/mounts', ''), 'lxcfs /proc/')) {
            return 'LXC';
        }
        if (\is_file('/mnt/wsl/resolv.conf')) {
            return 'WSL';
        }

        if (\is_file('/.dockerenv') || \is_file('/.dockerinit') || \str_contains(self::getContents('/proc/1/cgroup', ''), 'docker')) {
            return 'Docker';
        }

        // Try getting kernel modules
        $modules = [];
        if (\preg_match_all('/^(\S+)/m', self::getContents('/proc/modules', ''), $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $modules[] = $match[1];
            }
        }

        // Sometimes /proc/modules is missing what is in this dir on VMs
        $paths = \glob('/sys/bus/pci/drivers/*');
        if ($paths) {
            foreach ($paths as $name) {
                $modules[] = \basename($name);
            }
        }

        if (\in_array('vboxguest', $modules, true)) {
            return 'VirtualBox';
        }

        // VMware guest. Tested on debian under vmware fusion for mac...
        foreach (['vmw_balloon', 'vmwgfx', 'vmw_vmci'] as $name) {
            if (\in_array($name, $modules, true)) {
                return 'VMWare';
            }
        }

        // Xen
        if (\is_dir('/proc/xen')) {
            return 'Xen';
        }
        foreach (['xenfs', 'xen_gntdev', 'xen_evtchn', 'xen_blkfront', 'xen_netfront'] as $name) {
            if (\in_array($name, $modules, true)) {
                return 'Xen';
            }
        }

        // Hyper-V guest. Tested with Trusty under Client Hyper-V in Windows 10 Pro. Needs to be checked before KVM/QEMU!
        foreach (['hid_hyperv', 'hv_vmbus', 'hv_utils'] as $name) {
            if (\in_array($name, $modules, true)) {
                return 'Hyper-V';
            }
        }

        // Looks like it might be a KVM or QEMU guest! This is a bit lame since Xen can also use virtio but its less likely (?)
        foreach (['virtio', 'virtio_balloon', 'virtio_pci', 'virtio-pci', 'virtio_blk', 'virtio_net'] as $name) {
            if (\in_array($name, $modules, true)) {
                return 'Qemu/KVM';
            }
        }

        // idk
        return null;
    }

    /**
     * through /sys' interface to dmidecode.
     */
    public function getModel(): ?string
    {
        $info = [];

        $name = self::getContents('/sys/devices/virtual/dmi/id/board_name');
        if (!$name) {
            return null;
        }

        $vendor = self::getContents('/sys/devices/virtual/dmi/id/board_vendor');
        // Don't add vendor to the mix if the name starts with it
        if ($vendor && !\str_starts_with($name, $vendor)) {
            $info[] = $vendor;
        }

        $info[] = $name;

        $infoStr = \implode(' ', $info);

        $product = self::getContents('/sys/devices/virtual/dmi/id/product_name');
        // product name is usually bullshit, but *occasionally* it's a useful name of the computer, such as
        // dell latitude e6500 or hp z260
        if ($product && !\str_contains($name, $product) && !\str_contains($product, 'Filled')) {
            return $product.' ('.$infoStr.')';
        }

        return $infoStr;
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getUps(?string $cwd = null): ?Ups
    {
        $ups = (new Apcaccess())->run($cwd);
        if (null === $ups) {
            return null;
        }

        return new Ups($ups['name'], $ups['model'], $ups['batteryVolts'], $ups['batteryCharge'], $ups['timeLeft'], $ups['currentLoad'], $ups['status']);
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getPrinters(?string $cwd = null): ?array
    {
        $printers = (new Lpstat())->run($cwd);
        if (null === $printers) {
            return null;
        }

        $out = [];
        foreach ($printers as $printer) {
            $out[] = new Printer($printer['name'], $printer['enabled']);
        }

        return $out;
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getSamba(?string $cwd = null): ?Samba
    {
        $data = (new Smbstatus())->run($cwd);
        if (null === $data) {
            return null;
        }

        $files = [];
        foreach ($data['files'] as $file) {
            $files[] = new Samba\File(
                $file['pid'],
                \posix_getpwuid($file['uid'])['name'],
                $file['denyMode'],
                $file['access'],
                $file['rw'],
                $file['oplock'],
                $file['sharePath'],
                $file['name'],
                $file['time'],
            );
        }

        $services = [];
        foreach ($data['services'] as $service) {
            $services[] = new Samba\Service(
                $service['service'],
                $service['pid'],
                $service['machine'],
                $service['connectedAt'],
                $service['encryption'],
                $service['signing'],
            );
        }

        $connections = [];
        foreach ($data['connections'] as $connection) {
            $connections[] = new Samba\Connection(
                $connection['pid'],
                $connection['user'],
                $connection['group'],
                $connection['host'],
                $connection['ip'],
                $connection['protocolVersion'],
                $connection['encryption'],
                $connection['signing'],
            );
        }

        return new Samba($files, $services, $connections);
    }

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     */
    public function getSelinux(?string $cwd = null): ?Selinux
    {
        $data = (new Sestatus())->run($cwd);
        if (null === $data) {
            return null;
        }

        return new Selinux($data['enabled'], $data['mode'], $data['policy']);
    }
}
