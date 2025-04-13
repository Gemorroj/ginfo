<?php

namespace Ginfo\OS;

use Ginfo\Common;
use Ginfo\Exceptions\FatalException;
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
use Ginfo\Parsers\Apcaccess;
use Ginfo\Parsers\Free;
use Ginfo\Parsers\Hwpci;
use Ginfo\Parsers\Lpstat;
use Ginfo\Parsers\Mdadm;
use Ginfo\Parsers\ProcCpuinfo;
use Ginfo\Parsers\Sensors\Hddtemp;
use Ginfo\Parsers\Sensors\Hwmon;
use Ginfo\Parsers\Sensors\Ipmi;
use Ginfo\Parsers\Sensors\Mbmon;
use Ginfo\Parsers\Sensors\Nvidia;
use Ginfo\Parsers\Sensors\Sensors;
use Ginfo\Parsers\Sensors\ThermalZone;
use Ginfo\Parsers\Sestatus;
use Ginfo\Parsers\Smbstatus;
use Ginfo\Parsers\Systemd;
use Ginfo\Parsers\Who;

final class Linux extends OS
{
    /**
     * @throws FatalException
     */
    public function __construct()
    {
        if (!\is_dir('/sys') || !\is_dir('/proc')) {
            throw new FatalException('This needs access to /proc and /sys to work.');
        }
    }

    public function getMemory(): ?Memory
    {
        $data = Free::work();
        if (null === $data) {
            return null;
        }

        return new Memory(
            $data['total'],
            $data['used'],
            $data['free'],
            $data['shared'],
            $data['buffers'],
            $data['cached'],
            $data['swapTotal'],
            $data['swapUsed'],
            $data['swapFree']
        );
    }

    public function getCpu(): ?Cpu
    {
        $cpuInfo = ProcCpuinfo::work();
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

    public function getUptime(): ?float
    {
        $uptime = Common::getContents('/proc/uptime');

        if (null === $uptime) {
            return null;
        }

        return \round(\explode(' ', $uptime, 2)[0]);
    }

    public function getDrives(): ?array
    {
        $partitions = [];
        $partitionsContents = Common::getContents('/proc/partitions');
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
            if (1 !== \preg_match('/^(\d+)\s+\d+\s+\d+\s+\d+\s+(\d+)/', Common::getContents(\dirname($path, 2).'/stat'), $statMatches)) {
                $reads = null;
                $writes = null;
            } else {
                [, $reads, $writes] = $statMatches;
            }

            $type = '';
            if ('0' === Common::getContents(\dirname($path, 2).'/queue/rotational')) {
                if ('SD' === Common::getContents(\dirname($path).'/type')) {
                    $type = ' (SD)';
                } else {
                    $type = ' (SSD)';
                }
            }

            $namePartition = $parts[3];
            $p = \array_key_exists($namePartition, $partitions) && \is_array($partitions[$namePartition]) ? $partitions[$namePartition] : [];

            $drives[] = new Drive(
                Common::getContents(\dirname($path).'/model', 'Unknown').$type,
                '/dev/'.$namePartition,
                Common::getContents(\dirname($path, 2).'/size', '0') * 512,
                Common::getContents(\dirname($path).'/vendor'),
                $reads,
                $writes,
                $p,
            );
        }

        return $drives;
    }

    public function getMounts(): ?array
    {
        $contents = Common::getContents('/proc/mounts');
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

        return $mounts;
    }

    public function getRaids(): ?array
    {
        $data = Mdadm::work();
        if (null === $data) {
            return null;
        }

        $out = [];
        foreach ($data as $raid) {
            $drives = [];
            foreach ($raid['drives'] as $drive) {
                $drives[] = new Raid\Drive($drive['path'], $drive['state']);
            }

            $out[] = new Raid(
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

        return $out;
    }

    public function getSensors(): ?array
    {
        $return = [];

        $hddtempRes = Hddtemp::work();
        if ($hddtempRes) {
            $return = \array_merge($return, $hddtempRes);
        }

        $mbmonRes = Mbmon::work();
        if ($mbmonRes) {
            $return = \array_merge($return, $mbmonRes);
        }

        $sensorsRes = Sensors::work();
        if ($sensorsRes) {
            $return = \array_merge($return, $sensorsRes);
        }

        $hwmonRes = Hwmon::work();
        if ($hwmonRes) {
            $return = \array_merge($return, $hwmonRes);
        }

        $thermalZoneRes = ThermalZone::work();
        if ($thermalZoneRes) {
            $return = \array_merge($return, $thermalZoneRes);
        }

        $ipmi = Ipmi::work();
        if ($ipmi) {
            $return = \array_merge($return, $ipmi);
        }

        $nvidia = Nvidia::work();
        if ($nvidia) {
            $return = \array_merge($return, $nvidia);
        }

        // Laptop backlight percentage
        $paths = \glob('/sys/{devices/virtual,class}/backlight/*/max_brightness', \GLOB_NOSORT | \GLOB_BRACE);
        if ($paths) {
            foreach ($paths as $bl) {
                $max = Common::getContents($bl);
                $cur = Common::getContents(\dirname($bl).'/actual_brightness');
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
        $data = Hwpci::work(Hwpci::MODE_USB);
        if (null === $data) {
            return null;
        }

        $out = [];
        foreach ($data as $v) {
            $out[] = new Usb($v['vendor'], $v['name'], $v['speed']);
        }

        return $out;
    }

    public function getPci(): ?array
    {
        $data = Hwpci::work(Hwpci::MODE_PCI);
        if (null === $data) {
            return null;
        }

        $out = [];
        foreach ($data as $v) {
            $out[] = new Pci($v['vendor'], $v['name']);
        }

        return $out;
    }

    public function getLoad(): ?array
    {
        $load = \sys_getloadavg();

        return false === $load ? null : $load;
    }

    public function getNetwork(): ?array
    {
        $paths = \glob('/sys/class/net/*', \GLOB_NOSORT);
        if (!$paths) {
            return null;
        }

        $return = [];
        foreach ($paths as $path) {
            $speed = (int) Common::getContents($path.'/speed'); // Mbits/sec
            if ($speed) {
                $speed *= 1000000;
            }

            $operstateContents = Common::getContents($path.'/operstate');
            $state = \in_array($operstateContents, ['up', 'down'], true) ? $operstateContents : null;

            if (null === $state && \file_exists($path.'/carrier')) {
                $state = Common::getContents($path.'/carrier') ? 'up' : 'down';
            }

            // Try the weird ways of getting type (https://stackoverflow.com/a/16060638)
            $typeCode = Common::getContents($path.'/type');

            if ('772' === $typeCode) {
                $type = 'Loopback';
            } elseif ('65534' === $typeCode) {
                $type = 'Tunnel';
            } elseif ('776' === $typeCode) {
                $type = 'IPv6 in IPv4';
            } else {
                $typeContents = \mb_strtoupper(Common::getContents($path.'/device/modalias', ''));
                [$typeMatch] = \explode(':', $typeContents, 2);

                $ueventContents = Common::getIni($path.'/uevent');

                if ($ueventContents && isset($ueventContents['DEVTYPE'])) {
                    $type = \ucfirst($ueventContents['DEVTYPE']);
                    if (\in_array($typeMatch, ['PCI', 'USB'], true)) {
                        $type .= ' ('.$typeMatch.')';
                    }
                    $deviceUeventContents = Common::getIni($path.'/device/uevent');
                    if ($deviceUeventContents && isset($deviceUeventContents['DRIVER'])) {
                        $type .= ' ('.$deviceUeventContents['DRIVER'].')';
                    }
                } elseif (\in_array($typeMatch, ['PCI', 'USB'], true)) {
                    $type = 'Ethernet ('.$typeMatch.')';

                    $deviceUeventContents = Common::getIni($path.'/device/uevent');
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
                (int) Common::getContents($path.'/statistics/rx_bytes', '0'),
                (int) Common::getContents($path.'/statistics/rx_errors', '0'),
                (int) Common::getContents($path.'/statistics/rx_packets', '0')
            );
            $statsSent = new Network\Stats(
                (int) Common::getContents($path.'/statistics/tx_bytes', '0'),
                (int) Common::getContents($path.'/statistics/tx_errors', '0'),
                (int) Common::getContents($path.'/statistics/tx_packets', '0')
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
            $uevent = Common::getContents($b.'/uevent');
            if (null === $uevent) {
                continue;
            }

            $block = Common::parseKeyValueBlock($uevent, '=');

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
        $lines = Common::getLines('/proc/asound/cards');
        if (null === $lines) {
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
            $statusContents = Common::getContents($process);
            if (null === $statusContents) {
                continue;
            }

            $cmdlineContents = Common::getContents(\dirname($process).'/cmdline');
            $ioContents = Common::getContents(\dirname($process).'/io');

            $blockIo = $ioContents ? Common::parseKeyValueBlock($ioContents) : null;
            $blockStatus = Common::parseKeyValueBlock($statusContents);

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

    public function getServices(): ?array
    {
        $services = Systemd::work(Service::TYPE_SERVICE);
        $targets = Systemd::work(Service::TYPE_TARGET);
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
            $os = Common::getContents($releaseFile);
            if (null !== $os) {
                return $os;
            }
        }

        $lsbRelease = Common::getIni('/etc/lsb-release');
        if ($lsbRelease) {
            return $lsbRelease['DISTRIB_DESCRIPTION'];
        }

        $suseRelease = Common::getLines('/etc/SuSE-release');
        if ($suseRelease) {
            return $suseRelease[0];
        }

        $debianVersion = Common::getContents('/etc/debian_version');
        if (null !== $debianVersion) {
            return 'Debian '.$debianVersion;
        }

        return \PHP_OS;
    }

    public function getLoggedUsers(): ?array
    {
        return Who::work();
    }

    public function getVirtualization(): ?string
    {
        if (\is_file('/proc/vz/veinfo')) {
            return 'OpenVZ';
        }

        $biosVendor = Common::getContents('/sys/devices/virtual/dmi/id/bios_vendor');
        if ('Veertu' === $biosVendor) {
            return 'Veertu';
        }
        if (\str_starts_with($biosVendor, 'Parallels')) {
            return 'Parallels';
        }

        if (\str_contains(Common::getContents('/proc/mounts', ''), 'lxcfs /proc/')) {
            return 'LXC';
        }
        if (\is_file('/mnt/wsl/resolv.conf')) {
            return 'WSL';
        }

        if (\is_file('/.dockerenv') || \is_file('/.dockerinit') || \str_contains(Common::getContents('/proc/1/cgroup', ''), 'docker')) {
            return 'Docker';
        }

        // Try getting kernel modules
        $modules = [];
        if (\preg_match_all('/^(\S+)/m', Common::getContents('/proc/modules', ''), $matches, \PREG_SET_ORDER)) {
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
        if (Common::anyInArray(['vmw_balloon', 'vmwgfx', 'vmw_vmci'], $modules)) {
            return 'VMWare';
        }

        if (Common::anyInArray(['xenfs', 'xen_gntdev', 'xen_evtchn', 'xen_blkfront', 'xen_netfront'], $modules) || \is_dir('/proc/xen')) {
            return 'Xen';
        }

        // Hyper-V guest. Tested with Trusty under Client Hyper-V in Windows 10 Pro. Needs to be checked before KVM/QEMU!
        if (Common::anyInArray(['hid_hyperv', 'hv_vmbus', 'hv_utils'], $modules)) {
            return 'Hyper-V';
        }

        // Looks like it might be a KVM or QEMU guest! This is a bit lame since Xen can also use virtio but its less likely (?)
        if (Common::anyInArray(['virtio', 'virtio_balloon', 'virtio_pci', 'virtio-pci', 'virtio_blk', 'virtio_net'], $modules)) {
            return 'Qemu/KVM';
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
        $vendor = Common::getContents('/sys/devices/virtual/dmi/id/board_vendor');
        $name = Common::getContents('/sys/devices/virtual/dmi/id/board_name');
        $product = Common::getContents('/sys/devices/virtual/dmi/id/product_name');

        if (!$name) {
            return null;
        }

        // Don't add vendor to the mix if the name starts with it
        if ($vendor && !\str_starts_with($name, $vendor)) {
            $info[] = $vendor;
        }

        $info[] = $name;

        $infoStr = \implode(' ', $info);

        // product name is usually bullshit, but *occasionally* it's a useful name of the computer, such as
        // dell latitude e6500 or hp z260
        if ($product && !\str_contains($name, $product) && !\str_contains($product, 'Filled')) {
            return $product.' ('.$infoStr.')';
        }

        return $infoStr;
    }

    public function getUps(): ?Ups
    {
        $ups = Apcaccess::work();
        if (null === $ups) {
            return null;
        }

        return new Ups($ups['name'], $ups['model'], $ups['batteryVolts'], $ups['batteryCharge'], $ups['timeLeft'], $ups['currentLoad'], $ups['status']);
    }

    public function getPrinters(): ?array
    {
        $printers = Lpstat::work();
        if (null === $printers) {
            return null;
        }

        $out = [];
        foreach ($printers as $printer) {
            $out[] = new Printer($printer['name'], $printer['enabled']);
        }

        return $out;
    }

    public function getSamba(): ?Samba
    {
        $data = Smbstatus::work();
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

    public function getSelinux(): ?Selinux
    {
        $data = Sestatus::work();
        if (null === $data) {
            return null;
        }

        return new Selinux($data['enabled'], $data['mode'], $data['policy']);
    }
}
