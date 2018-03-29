<?php

/**
 * This file is part of Linfo (c) 2010 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Linfo\OS;

use Linfo\Common;
use Linfo\Exceptions\FatalException;
use Linfo\Extension\Smbstatus;
use Linfo\Info\Cpu;
use Linfo\Info\Memory;
use Linfo\Info\Pci;
use Linfo\Info\SoundCard;
use Linfo\Info\Usb;
use Linfo\Parsers\Apcaccess;
use Linfo\Parsers\Lpstat;
use Linfo\Parsers\Free;
use Linfo\Parsers\Sestatus;
use Linfo\Parsers\Temps\Hwmon;
use Linfo\Parsers\Hwpci;
use Linfo\Parsers\Temps\Ipmi;
use Linfo\Parsers\Mdadm;
use Linfo\Parsers\Temps\Nvidia;
use Linfo\Parsers\Temps\Sensord;
use Linfo\Parsers\Temps\Hddtemp;
use Linfo\Parsers\Temps\Mbmon;
use Linfo\Parsers\Systemd;
use Linfo\Parsers\Temps\ThermalZone;
use Linfo\Parsers\Who;


class Linux extends OS
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


    public function getMemory() : ?Memory
    {
        $data = Free::work();
        if (null === $data) {
            return null;
        }

        return (new Memory())
            ->setTotal($data['total'])
            ->setFree($data['free'])
            ->setUsed($data['used'])
            ->setShared($data['shared'])
            ->setBuffers($data['buffers'])
            ->setCached($data['cached'])
            ->setSwapTotal($data['swapTotal'])
            ->setSwapUsed($data['swapUsed'])
            ->setSwapFree($data['swapFree']);
    }

    public function getCpu(): ?Cpu
    {
        $cpuInfo = Common::getContents('/proc/cpuinfo');
        if (null === $cpuInfo) {
            return null;
        }

        $cpuData = [];
        foreach (\explode("\n\n", $cpuInfo) as $block) {
            $cpuData[] = Common::parseKeyValueBlock($block);
        }


        $detectPhysical = function (array $cpuData) : int {
            $out = [];
            foreach ($cpuData as $block) {
                if (isset($out[$block['physical id']])) {
                    $out[$block['physical id']]++;
                } else {
                    $out[$block['physical id']] = 1;
                }
            }
            return \count($out);
        };
        $detectCores = function (array $cpuData) : int {
            $out = [];
            foreach ($cpuData as $block) {
                $out[$block['physical id']] = $block['cpu cores'];
            }
            return \array_sum($out);
        };

        $detectInfo = function (array $cpuData) : array {
            $out = [];
            foreach ($cpuData as $block) {
                // overwrite data for physical processors
                $out[$block['physical id']] = (new Cpu\Processor())
                    ->setModel($block['model name'])
                    ->setSpeed($block['cpu MHz'])
                    ->setL2Cache((float)$block['cache size'] * 1024) // L2 cache, drop KB
                    ->setFlags(\explode(' ', $block['flags']));
            }
            return $out;
        };

        $cores = $detectCores($cpuData);
        $virtual = \count($cpuData);

        return (new Cpu())
            ->setPhysical($detectPhysical($cpuData))
            ->setVirtual($virtual)
            ->setCores($cores)
            ->setHyperThreading($cores < $virtual)
            ->setProcessors($detectInfo($cpuData));
    }


    public function getUptime(): ?int
    {
        $uptime = Common::getContents('/proc/uptime');

        if (null === $uptime) {
            return null;
        }

        return \round(\explode(' ', $uptime, 2)[0]);
    }


    public function getPartitions(): ?array
    {
        $partitions = [];
        $partitionsContents = Common::getContents('/proc/partitions');
        if (null === $partitionsContents) {
            return null;
        }

        if (\preg_match_all('/(\d+)\s+([a-z]{3})(\d+)$/m', $partitionsContents, $partitionsMatch, \PREG_SET_ORDER) > 0) {
            foreach ($partitionsMatch as $partition) {
                $partitions[$partition[2]][] = [
                    'size' => $partition[1] * 1024,
                    'number' => $partition[3],
                ];
            }
        }

        $paths = \glob('/sys/block/*/device/model', \GLOB_NOSORT);
        if (false === $paths) {
            return null;
        }

        $drives = [];
        foreach ($paths as $path) {
            $parts = \explode('/', $path);

            // Attempt getting read/write stats
            if (\preg_match('/^(\d+)\s+\d+\s+\d+\s+\d+\s+(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+$/', Common::getContents(\dirname(\dirname($path)) . '/stat'), $statMatches) !== 1) {
                $reads = null;
                $writes = null;
            } else {
                list(, $reads, $writes) = $statMatches;
            }

            $drives[] = [
                'name' => Common::getContents($path, 'Unknown') . (Common::getContents(\dirname(\dirname($path)) . '/queue/rotational') === '0' ? ' (SSD)' : ''),
                'vendor' => Common::getContents(\dirname($path) . '/vendor', 'Unknown'),
                'device' => '/dev/' . $parts[3],
                'reads' => $reads,
                'writes' => $writes,
                'size' => Common::getContents(\dirname(\dirname($path)) . '/size', 0) * 512,
                'partitions' => \array_key_exists($parts[3], $partitions) && \is_array($partitions[$parts[3]]) ? $partitions[$parts[3]] : null,
            ];
        }

        return $drives;
    }

    public function getMounts(): ?array
    {
        $contents = Common::getContents('/proc/mounts');
        if (null === $contents) {
            return null;
        }

        if (\preg_match_all('/^(\S+) (\S+) (\S+) (.+) \d \d$/m', $contents, $match, \PREG_SET_ORDER) === false) {
            return null;
        }

        $mounts = [];
        foreach ($match as $mount) {
            // Spaces and other things in the mount path are escaped C style. Fix that.
            $mount[2] = \stripcslashes($mount[2]);

            $size = \disk_total_space($mount[2]);
            $free = \disk_free_space($mount[2]);
            $used = $size !== false && $free !== false ? $size - $free : false;

            $mounts[] = [
                'device' => $mount[1],
                'mount' => $mount[2],
                'type' => $mount[3],
                'size' => $size,
                'used' => $used,
                'free' => $free,
                'freePercent' => ($free !== false && $size !== false && $size > 0 ? \round($free / $size, 2) * 100 : null),
                'usedPercent' => ($used !== false && $size !== false && $size > 0 ? \round($used / $size, 2) * 100 : null),
                'options' => \explode(',', $mount[4]),
            ];
        }

        return $mounts;
    }

    /**
     * mdadm wrapper
     * @return array|null
     */
    public function getRaid(): ?array
    {
        return Mdadm::work();
    }


    public function getTemps(): ?array
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

        $sensordRes = Sensord::work();
        if ($sensordRes) {
            $return = \array_merge($return, $sensordRes);
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
        foreach (\glob('/sys/{devices/virtual,class}/backlight/*/max_brightness', \GLOB_NOSORT | \GLOB_BRACE) as $bl) {
            $max = Common::getContents($bl);
            $cur = Common::getContents(\dirname($bl) . '/actual_brightness');
            if (null === $cur) {
                continue;
            }

            if ($max < 0 || $cur < 0) {
                continue;
            }
            $return[] = [
                'name' => 'Backlight brightness',
                'temp' => \round($cur / $max, 2) * 100,
                'unit' => '%',
                'path' => null,
            ];
        }

        return $return ?: null;
    }


    public function getUsb(): ?array
    {
        $data = Hwpci::work(Hwpci::MODE_USB);
        if (null === $data) {
            return null;
        }

        $out = [];
        foreach ($data as $v) {
            $out[] = (new Usb())
                ->setVendor($v['vendor'])
                ->setName($v['device']);
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
            $out[] = (new Pci())
                ->setVendor($v['vendor'])
                ->setName($v['device']);
        }
        return $out;
    }


    public function getLoad(): ?array
    {
        return \sys_getloadavg();
    }


    public function getNetwork(): ?array
    {
        $paths = \glob('/sys/class/net/*', \GLOB_NOSORT);
        if (false === $paths) {
            return null;
        }

        $return = [];
        foreach ($paths as $path) {
            $nic = \basename($path);

            $operstateContents = Common::getContents($path . '/operstate');
            switch ($operstateContents) {
                case 'down':
                case 'up':
                case 'unknown':
                    $state = $operstateContents;
                    break;

                default:
                    $state = 'unknown';
                    break;
            }

            if ('unknown' === $state && \file_exists($path . '/carrier')) {
                $carrier = Common::getContents($path . '/carrier');
                if (!empty($carrier)) {
                    $state = 'up';
                } else {
                    $state = 'down';
                }
            }

            // Try the weird ways of getting type (https://stackoverflow.com/a/16060638)
            $typeCode = Common::getContents($path . '/type');

            if ($typeCode === '772') {
                $type = 'Loopback';
            } elseif ($typeCode === '65534') {
                $type = 'Tunnel';
            } elseif ($typeCode === '776') {
                $type = 'IPv6 in IPv4';
            } else {
                $typeContents = \mb_strtoupper(Common::getContents($path . '/device/modalias'));
                list($typeMatch) = \explode(':', $typeContents, 2);

                if (\in_array($typeMatch, ['PCI', 'USB'])) {
                    $type = 'Ethernet (' . $typeMatch . ')';

                    if (($ueventContents = \parse_ini_file($path . '/device/uevent')) && isset($ueventContents['DRIVER'])) {
                        $type .= ' (' . $ueventContents['DRIVER'] . ')';
                    }
                } elseif ($typeMatch === 'VIRTIO') {
                    $type = 'VirtIO';
                } elseif ($typeContents === 'XEN:VIF') {
                    $type = 'Xen (VIF)';
                } elseif ($typeContents === 'XEN-BACKEND:VIF') {
                    $type = 'Xen Backend (VIF)';
                } elseif (\is_dir($path . '/bridge')) {
                    $type = 'Bridge';
                } elseif (\is_dir($path . '/bonding')) {
                    $type = 'Bond';
                } else {
                    $type = 'Unknown';
                }

                // TODO find some way of finding out what provides the virt-specific kvm vnet devices
            }

            $speed = Common::getContents($path . '/speed');

            $return[$nic] = [
                'recieved' => [
                    'bytes' => Common::getContents($path . '/statistics/rx_bytes'),
                    'errors' => Common::getContents($path . '/statistics/rx_errors'),
                    'packets' => Common::getContents($path . '/statistics/rx_packets'),
                ],
                'sent' => [
                    'bytes' => Common::getContents($path . '/statistics/tx_bytes'),
                    'errors' => Common::getContents($path . '/statistics/tx_errors'),
                    'packets' => Common::getContents($path . '/statistics/tx_packets'),
                ],
                'state' => $state,
                'type' => $type,
                'portSpeed' => $speed,
            ];
        }

        return $return;
    }


    public function getBattery(): ?array
    {
        $paths = \glob('/sys/class/power_supply/BAT*', \GLOB_NOSORT);
        if (false === $paths) {
            return null;
        }

        $return = [];
        foreach ($paths as $b) {
            foreach ([$b . '/manufacturer', $b . '/status'] as $f) {
                if (!\is_file($f)) {
                    continue 2; // http://php.net/continue
                }
            }

            if (\is_file($b . '/energy_full')) {
                $chargeFull = Common::getContents($b . '/energy_full');
                $chargeNow = Common::getContents($b . '/energy_now');
            } else if (\is_file($b . '/charge_full')) {
                $chargeFull = Common::getContents($b . '/charge_full');
                $chargeNow = Common::getContents($b . '/charge_now');
            } else {
                continue;
            }

            $percentage = $chargeNow != 0 && $chargeFull != 0 ? (\round($chargeNow / $chargeFull, 4) * 100) : '?';

            $return[] = [
                'charge_full' => $chargeFull,
                'charge_now' => $chargeNow,
                'percentage' => (\is_numeric($percentage) && $percentage > 100 ? 100 : $percentage),
                'device' => Common::getContents($b . '/manufacturer') . ' ' . Common::getContents($b . '/model_name', 'Unknown'),
                'state' => Common::getContents($b . '/status', 'Unknown'),
            ];
        }

        return $return;
    }

    public function getWifi(): ?array
    {
        $contents = Common::getContents('/proc/net/wireless');
        if (null === $contents) {
            return null;
        }

        if (false === \preg_match_all('/^ (\S+)\:\s*(\d+)\s*(\S+)\s*(\S+)\s*(\S+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*$/m', $contents, $match, \PREG_SET_ORDER)) {
            return null;
        }

        $return = [];
        foreach ($match as $wlan) {
            $return[] = [
                'device' => $wlan[1],
                'status' => $wlan[2],
                'qualityLink' => $wlan[3],
                'qualityLevel' => $wlan[4],
                'qualityNoise' => $wlan[5],
                'disNwid' => $wlan[6],
                'disCrypt' => $wlan[7],
                'disFrag' => $wlan[8],
                'disRetry' => $wlan[9],
                'disMisc' => $wlan[10],
                'misBeac' => $wlan[11],
            ];
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
        for ($i = 0, $l = \count($lines); $i < $l; $i +=2) {
            $name = \trim(\explode(']:', $lines[$i], 2)[1]);
            $vendor = \trim(\explode(' at ', $lines[$i], 2)[0]);

            $cards[] = (new SoundCard())
                ->setVendor($vendor)
                ->setName($name);
        }

        return $cards;
    }


    public function getProcesses(): ?array
    {
        $processes = \glob('/proc/*/status', \GLOB_NOSORT);
        if (null === $processes) {
            return null;
        }

        $result = [];
        foreach ($processes as $process) {
            $statusContents = Common::getContents($process);
            if (null === $statusContents) {
                $result[] = [
                    'name' => null,
                    'commandLine' => null,
                    'threads' => null,
                    'state' => null,
                    'workingSet' => null,
                    'pid' => \basename(\dirname($process)),
                ];
                continue;
            }

            $info = Common::parseKeyValueBlock($statusContents);

            $cmdlineContents = Common::getContents(\dirname($process) . '/cmdline');
            $uid = \explode("\t", $info['Uid'], 2)[0];
            $user = \posix_getpwuid($uid);

            if (isset($info['VmSize'])) {
                $vmSize = (float)$info['VmSize']; // drop kB
                $vmSize *= 1024;
            } else {
                $vmSize = null;
            }

            if (isset($info['VmPeak'])) {
                $vmPeak = (float)$info['VmPeak']; // drop kB
                $vmPeak *= 1024;
            } else {
                $vmPeak = null;
            }

            $result[] = [
                'name' => $info['Name'],
                'commandLine' => null !== $cmdlineContents ? \str_replace("\0", ' ', $cmdlineContents) : null,
                'threads' => $info['Threads'],
                'state' => $info['State'],
                'memory' => $vmSize,
                'peakMemory' => $vmPeak,
                'pid' => \basename(\dirname($process)),
                'user' => $user ? $user['name'] : $uid,
            ];
        }

        return $result;
    }


    /**
     * Systemd wrapper
     * @return array|null
     */
    public function getServices(): ?array
    {
        return Systemd::work();
    }


    public function getOsName(): string
    {
        $stringReleases = [
            '/etc/centos-release',
            '/etc/redhat-release',
            '/etc/fedora-release',
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


        $lsbRelease = Common::getContents('/etc/lsb-release');
        if (null !== $lsbRelease) {
            return \parse_ini_string($lsbRelease)['DISTRIB_DESCRIPTION'];
        }

        $suseRelease = Common::getLines('/etc/SuSE-release');
        if (null !== $suseRelease) {
            return $suseRelease[0];
        }

        $debianVersion = Common::getContents('/etc/debian_version');
        if (null !== $debianVersion) {
            return 'Debian ' . $debianVersion;
        }

        return \php_uname('s');
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

        if (Common::getContents('/sys/devices/virtual/dmi/id/bios_vendor') === 'Veertu') {
            return 'Veertu';
        }

        if (\mb_strpos(Common::getContents('/proc/mounts', ''), 'lxcfs /proc/') !== false) {
            return 'LXC';
        }

        if (\is_file('/.dockerenv') || \is_file('/.dockerinit') || \mb_strpos(Common::getContents('/proc/1/cgroup', ''), 'docker') !== false) {
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
        foreach (\glob('/sys/bus/pci/drivers/*') as $name) {
            $modules[] = \basename($name);
        }

        // VMware guest. Tested on debian under vmware fusion for mac...
        if (Common::anyInArray(['vmw_balloon', 'vmwgfx', 'vmw_vmci'], $modules)) {
            return 'VMWare';
        }

        if (Common::anyInArray(['xenfs', 'xen_gntdev', 'xen_evtchn', 'xen_blkfront', 'xen_netfront'], $modules) || \is_dir('/proc/xen')) {
            return 'Xen';
        }

        if (\in_array('vboxguest', $modules)) {
            return 'VirtualBox';
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
     * through /sys' interface to dmidecode
     */
    public function getModel() : ?string
    {
        $info = [];
        $vendor = Common::getContents('/sys/devices/virtual/dmi/id/board_vendor');
        $name = Common::getContents('/sys/devices/virtual/dmi/id/board_name');
        $product = Common::getContents('/sys/devices/virtual/dmi/id/product_name');

        if (!$name) {
            return null;
        }

        // Don't add vendor to the mix if the name starts with it
        if ($vendor && \mb_strpos($name, $vendor) !== 0) {
            $info[] = $vendor;
        }

        $info[] = $name;

        $infoStr = \implode(' ', $info);

        // product name is usually bullshit, but *occasionally* it's a useful name of the computer, such as
        // dell latitude e6500 or hp z260
        if ($product && \mb_strpos($name, $product) === false && \mb_strpos($product, 'Filled') === false) {
            return $product . ' (' . $infoStr . ')';
        } else {
            return $infoStr;
        }
    }


    public function getUps() : ?array
    {
        return Apcaccess::work();
    }

    public function getPrinters() : ?array
    {
        return Lpstat::work();
    }

    public function getSamba() : ?array
    {
        return Smbstatus::work();
    }

    public function getSelinux() : ?array
    {
        return Sestatus::work();
    }
}
