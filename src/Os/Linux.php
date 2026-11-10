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

        return self::parseUptime($uptime);
    }

    public function getDrives(): ?array
    {
        $partitionsContents = self::getContents('/proc/partitions');
        if (null === $partitionsContents) {
            return null;
        }

        $partitions = self::parsePartitions($partitionsContents);

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
                $reads = (float) $statMatches[1];
                $writes = (float) $statMatches[2];
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
                (float) self::getContents(\dirname($path, 2).'/size', '0') * 512,
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

        $lines = self::parseMountLines($contents);
        if (null === $lines) {
            return null;
        }

        $mounts = [];
        foreach ($lines as $line) {
            $mountPoint = $line['mount'];

            if (\is_readable($mountPoint)) {
                $size = \disk_total_space($mountPoint);
                $size = false === $size ? null : $size;

                $free = \disk_free_space($mountPoint);
                $free = false === $free ? null : $free;

                $used = (null !== $size && null !== $free) ? $size - $free : null;
            } else {
                $size = $free = $used = null;
            }

            $mounts[] = new Mount(
                $line['device'],
                $mountPoint,
                $line['fstype'],
                $size,
                $used,
                $free,
                self::percent($free, $size),
                self::percent($used, $size),
                $line['options']
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
                (int) $raid['level'],
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
                if (null === $cur || null === $max) {
                    continue;
                }

                $maxValue = (float) $max;
                $curValue = (float) $cur;
                if ($maxValue <= 0 || $curValue < 0) {
                    continue;
                }
                $return[] = [
                    'name' => 'Backlight brightness',
                    'value' => \round($curValue / $maxValue * 100, 2),
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

        return self::parseLoad($load);
    }

    public function getNetwork(): ?array
    {
        $paths = \glob('/sys/class/net/*', \GLOB_NOSORT);
        if (!$paths) {
            return null;
        }

        $return = [];
        foreach ($paths as $path) {
            $speed = self::parseSpeed(self::getContents($path.'/speed'));

            $state = self::parseNetworkState(
                self::getContents($path.'/operstate'),
                \file_exists($path.'/carrier'),
                self::getContents($path.'/carrier'),
            );

            // Try the weird ways of getting type (https://stackoverflow.com/a/16060638)
            $ueventContents = @\parse_ini_file($path.'/uevent');
            $deviceUeventContents = @\parse_ini_file($path.'/device/uevent');

            $type = self::parseNetworkType(
                self::getContents($path.'/type'),
                \mb_strtoupper(self::getContents($path.'/device/modalias', '')),
                \is_array($ueventContents) && isset($ueventContents['DEVTYPE']) ? (string) $ueventContents['DEVTYPE'] : null,
                \is_array($deviceUeventContents) && isset($deviceUeventContents['DRIVER']) ? (string) $deviceUeventContents['DRIVER'] : null,
                \is_dir($path.'/bridge'),
                \is_dir($path.'/bonding'),
            );

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
                $block['POWER_SUPPLY_MODEL_NAME'] ?? '',
                $block['POWER_SUPPLY_MANUFACTURER'] ?? '',
                $block['POWER_SUPPLY_STATUS'] ?? '',
                (int) ($block['POWER_SUPPLY_CAPACITY'] ?? 0),
                (int) ($block['POWER_SUPPLY_VOLTAGE_NOW'] ?? 0),
                $block['POWER_SUPPLY_TECHNOLOGY'] ?? null,
                isset($block['POWER_SUPPLY_ENERGY_NOW']) ? (int) $block['POWER_SUPPLY_ENERGY_NOW'] : null,
                isset($block['POWER_SUPPLY_ENERGY_FULL']) ? (int) $block['POWER_SUPPLY_ENERGY_FULL'] : null,
                isset($block['POWER_SUPPLY_CHARGE_NOW']) ? (int) $block['POWER_SUPPLY_CHARGE_NOW'] : null,
                isset($block['POWER_SUPPLY_CHARGE_FULL']) ? (int) $block['POWER_SUPPLY_CHARGE_FULL'] : null
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

        return self::parseSoundCards($lines);
    }

    private static function percent(?float $part, ?float $total): ?float
    {
        if (null === $part || null === $total || $total <= 0) {
            return null;
        }

        return \round($part / $total * 100, 2);
    }

    private static function parseSpeed(?string $raw): ?float
    {
        if (null === $raw || !\is_numeric($raw)) {
            return null;
        }

        $speedMb = (int) $raw;
        // 4294967295 is -1 as unsigned 32-bit, used for virtual/no-link interfaces
        if ($speedMb <= 0 || 4294967295 === $speedMb) {
            return null;
        }

        return (float) ($speedMb * 1000000); // Mbits/sec -> bit/s
    }

    /**
     * @param string[] $lines
     *
     * @return SoundCard[]
     */
    private static function parseSoundCards(array $lines): array
    {
        $cards = [];
        for ($i = 0, $l = \count($lines); $i + 1 < $l; $i += 2) {
            if (!\str_contains($lines[$i], ']:')) {
                continue;
            }
            $name = \trim(\explode(']:', $lines[$i], 2)[1]);
            $vendor = \trim(\explode(' at ', $lines[$i + 1], 2)[0]);

            $cards[] = new SoundCard($vendor, $name);
        }

        return $cards;
    }

    private static function parseUptime(string $content): ?int
    {
        $content = \trim($content);
        if ('' === $content) {
            return null;
        }

        return (int) $content;
    }

    /**
     * @return array{0: float, 1: float, 2: float}|null
     */
    private static function parseLoad(string $content): ?array
    {
        $parts = \preg_split('/\s+/', \trim($content)) ?: [];
        if (!isset($parts[0], $parts[1], $parts[2]) || !\is_numeric($parts[0]) || !\is_numeric($parts[1]) || !\is_numeric($parts[2])) {
            return null;
        }

        return [(float) $parts[0], (float) $parts[1], (float) $parts[2]];
    }

    /**
     * @return array<string, Drive\Partition[]>
     */
    private static function parsePartitions(string $contents): array
    {
        $partitions = [];
        if (\preg_match_all('/(\d+)\s+([a-z]{3}|nvme\d+n\d+|[a-z]+\d+)(p?\d+)$/m', $contents, $matches, \PREG_SET_ORDER) > 0) {
            foreach ($matches as $partition) {
                $partitions[$partition[2]][] = new Drive\Partition(
                    (float) ($partition[1] * 1024),
                    $partition[2].$partition[3],
                );
            }
        }

        return $partitions;
    }

    /**
     * @return array<int, array{device: string, mount: string, fstype: string, options: string[]}>|null
     */
    private static function parseMountLines(string $contents): ?array
    {
        if (false === \preg_match_all('/^(\S+) (\S+) (\S+) (.+) \d \d$/m', $contents, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $lines = [];
        foreach ($matches as $match) {
            $lines[] = [
                'device' => $match[1],
                // Spaces and other things in the mount path are escaped C style. Fix that.
                'mount' => \stripcslashes($match[2]),
                'fstype' => $match[3],
                'options' => \explode(',', $match[4]),
            ];
        }

        return $lines;
    }

    private static function parseModel(?string $name, ?string $vendor, ?string $product): ?string
    {
        if (!$name) {
            return null;
        }

        $info = [];
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

    private static function parseNetworkState(?string $operstate, bool $carrierExists, ?string $carrier): ?string
    {
        if (\in_array($operstate, ['up', 'down'], true)) {
            return $operstate;
        }

        if ($carrierExists) {
            return $carrier ? 'up' : 'down';
        }

        return null;
    }

    private static function parseNetworkType(
        ?string $typeCode,
        string $typeContents,
        ?string $deviceType,
        ?string $driver,
        bool $hasBridge,
        bool $hasBonding,
    ): ?string {
        if ('772' === $typeCode) {
            return 'Loopback';
        }
        if ('65534' === $typeCode) {
            return 'Tunnel';
        }
        if ('776' === $typeCode) {
            return 'IPv6 in IPv4';
        }

        [$typeMatch] = \explode(':', $typeContents, 2);

        if (null !== $deviceType) {
            $type = \ucfirst($deviceType);
            if (\in_array($typeMatch, ['PCI', 'USB'], true)) {
                $type .= ' ('.$typeMatch.')';
            }
            if (null !== $driver) {
                $type .= ' ('.$driver.')';
            }

            return $type;
        }

        if (\in_array($typeMatch, ['PCI', 'USB'], true)) {
            $type = 'Ethernet ('.$typeMatch.')';
            if (null !== $driver) {
                $type .= ' ('.$driver.')';
            }

            return $type;
        }
        if ('VIRTIO' === $typeMatch) {
            return 'VirtIO';
        }
        if ('XEN:VIF' === $typeContents) {
            return 'Xen (VIF)';
        }
        if ('XEN-BACKEND:VIF' === $typeContents) {
            return 'Xen Backend (VIF)';
        }
        if ($hasBridge) {
            return 'Bridge';
        }
        if ($hasBonding) {
            return 'Bond';
        }

        return null;
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

            $data = self::parseProcess(
                $statusContents,
                self::getContents(\dirname($process).'/cmdline'),
                self::getContents(\dirname($process).'/io'),
            );
            if (null === $data) {
                continue;
            }

            $user = \posix_getpwuid($data['uid']);

            $result[] = new Process(
                $data['name'],
                $data['pid'],
                $data['cmdline'],
                $data['threads'],
                $data['state'],
                $data['vmSize'],
                $data['vmPeak'],
                $user ? $user['name'] : (string) $data['uid'],
                $data['readBytes'],
                $data['writeBytes'],
            );
        }

        return $result;
    }

    /**
     * @return array{
     *     name: string,
     *     pid: int,
     *     uid: int,
     *     cmdline: string|null,
     *     threads: int,
     *     state: string,
     *     vmSize: float|null,
     *     vmPeak: float|null,
     *     readBytes: float|null,
     *     writeBytes: float|null,
     * }|null
     */
    private static function parseProcess(string $statusContents, ?string $cmdlineContents, ?string $ioContents): ?array
    {
        $status = self::parseKeyValueBlock($statusContents);
        if (!isset($status['Name'], $status['Pid'], $status['Uid'], $status['Threads'], $status['State'])) {
            return null;
        }

        $io = null !== $ioContents ? self::parseKeyValueBlock($ioContents) : [];

        return [
            'name' => $status['Name'],
            'pid' => (int) $status['Pid'],
            'uid' => (int) \explode("\t", $status['Uid'], 2)[0],
            'cmdline' => null !== $cmdlineContents ? \str_replace("\0", ' ', $cmdlineContents) : null,
            'threads' => (int) $status['Threads'],
            'state' => $status['State'],
            'vmSize' => isset($status['VmSize']) ? (float) $status['VmSize'] * 1024 : null,
            'vmPeak' => isset($status['VmPeak']) ? (float) $status['VmPeak'] * 1024 : null,
            'readBytes' => isset($io['read_bytes']) ? (float) $io['read_bytes'] : null,
            'writeBytes' => isset($io['write_bytes']) ? (float) $io['write_bytes'] : null,
        ];
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

        $release = null;
        foreach ($stringReleases as $releaseFile) {
            $release = self::getContents($releaseFile);
            if (null !== $release) {
                break;
            }
        }

        return self::parseOsName(
            $release,
            @\parse_ini_file('/etc/lsb-release') ?: null,
            @\file('/etc/SuSE-release', \FILE_SKIP_EMPTY_LINES) ?: null,
            self::getContents('/etc/debian_version'),
        );
    }

    /**
     * @param array<string, mixed>|null $lsbRelease
     * @param string[]|null             $suseRelease
     */
    private static function parseOsName(?string $release, ?array $lsbRelease, ?array $suseRelease, ?string $debianVersion): string
    {
        if (null !== $release) {
            return $release;
        }

        if (null !== $lsbRelease && isset($lsbRelease['DISTRIB_DESCRIPTION'])) {
            return (string) $lsbRelease['DISTRIB_DESCRIPTION'];
        }

        if (null !== $suseRelease && [] !== $suseRelease) {
            return (string) $suseRelease[0];
        }

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
        $container = self::detectContainer(
            self::getContents('/run/systemd/container'),
            \file_exists('/run/.containerenv'),
            \file_exists('/.dockerenv') || \file_exists('/.dockerinit') || \str_contains(self::getContents('/proc/1/cgroup', ''), 'docker'),
            false !== \getenv('KUBERNETES_SERVICE_HOST') || \is_dir('/var/run/secrets/kubernetes.io/serviceaccount'),
            self::getContents('/proc/sys/kernel/osrelease'),
            \file_exists('/proc/vz') && !\file_exists('/proc/bc'),
            \str_contains(self::getContents('/proc/mounts', ''), 'lxcfs /proc/'),
        );
        if (null !== $container) {
            return $container;
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

        return self::detectVm(
            self::getContents('/sys/class/dmi/id/product_name'),
            self::getContents('/sys/class/dmi/id/sys_vendor'),
            self::getContents('/sys/class/dmi/id/board_vendor'),
            self::getContents('/sys/class/dmi/id/bios_vendor'),
            self::getContents('/sys/class/dmi/id/product_version'),
            self::getContents('/sys/hypervisor/type'),
            \is_dir('/proc/xen'),
            $modules,
        );
    }

    private static function detectContainer(
        ?string $containerManager,
        bool $hasPodmanEnv,
        bool $hasDockerEnv,
        bool $hasKubernetes,
        ?string $osRelease,
        bool $hasOpenVz,
        bool $hasLxcFs,
    ): ?string {
        if ($hasOpenVz) {
            return 'OpenVZ';
        }

        if (null !== $osRelease && (\str_contains($osRelease, 'Microsoft') || \str_contains($osRelease, 'WSL'))) {
            return 'WSL';
        }

        $manager = null !== $containerManager ? \strtolower(\trim($containerManager)) : '';
        if ('' !== $manager && 'oci' !== $manager) {
            return match ($manager) {
                'lxc', 'lxc-libvirt' => 'LXC',
                'systemd-nspawn' => 'systemd-nspawn',
                'docker' => 'Docker',
                'podman' => 'Podman',
                'rkt' => 'RKT',
                'pouch' => 'Pouch',
                'wsl' => 'WSL',
                'proot' => 'PRoot',
                default => 'Container (other)',
            };
        }

        if ($hasKubernetes) {
            return 'Kubernetes';
        }
        if ($hasPodmanEnv) {
            return 'Podman';
        }
        if ($hasDockerEnv) {
            return 'Docker';
        }
        if ($hasLxcFs) {
            return 'LXC';
        }
        if ('oci' === $manager) {
            return 'Container (other)';
        }

        return null;
    }

    /**
     * @param string[] $modules
     */
    private static function detectVm(
        ?string $productName,
        ?string $sysVendor,
        ?string $boardVendor,
        ?string $biosVendor,
        ?string $productVersion,
        ?string $hypervisorType,
        bool $hasXenDir,
        array $modules,
    ): ?string {
        // EC2 bare metal instances are not virtualized
        if (null !== $sysVendor && \str_starts_with($sysVendor, 'Amazon EC2')
            && null !== $productName && \str_contains($productName, '.metal')) {
            return null;
        }

        $byDmi = self::detectVirtualizationByDmi($productName, $sysVendor, $boardVendor, $biosVendor, $productVersion);
        if (null !== $byDmi) {
            return $byDmi;
        }

        if ('xen' === $hypervisorType) {
            return 'Xen';
        }
        if ($hasXenDir) {
            return 'Xen';
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

    private static function detectVirtualizationByDmi(
        ?string $productName,
        ?string $sysVendor,
        ?string $boardVendor,
        ?string $biosVendor,
        ?string $productVersion,
    ): ?string {
        static $table = [
            'KVM' => 'Qemu/KVM',
            'OpenStack' => 'OpenStack',
            'KubeVirt' => 'KubeVirt',
            'Alibaba Cloud' => 'Alibaba Cloud',
            'Amazon EC2' => 'Amazon EC2',
            'QEMU' => 'Qemu/KVM',
            'VMware' => 'VMWare',
            'VMW' => 'VMWare',
            'innotek GmbH' => 'VirtualBox',
            'VirtualBox' => 'VirtualBox',
            'Oracle Corporation' => 'VirtualBox',
            'OracleCloud' => 'Oracle Cloud',
            'Xen' => 'Xen',
            'Bochs' => 'Bochs',
            'Parallels' => 'Parallels',
            'BHYVE' => 'bhyve',
            'Hyper-V' => 'Hyper-V',
            'Microsoft Corporation' => 'Hyper-V',
            'Apple Virtualization' => 'Apple Virtualization',
            'Google Compute Engine' => 'Google Compute Engine',
            'DigitalOcean' => 'DigitalOcean',
            'Veertu' => 'Veertu',
        ];

        foreach ([$productName, $sysVendor, $boardVendor, $biosVendor, $productVersion] as $value) {
            if (null === $value) {
                continue;
            }

            foreach ($table as $prefix => $virtualization) {
                if (\str_starts_with($value, (string) $prefix)) {
                    return $virtualization;
                }
            }
        }

        return null;
    }

    /**
     * through /sys' interface to dmidecode.
     */
    public function getModel(): ?string
    {
        return self::parseModel(
            self::getContents('/sys/devices/virtual/dmi/id/board_name'),
            self::getContents('/sys/devices/virtual/dmi/id/board_vendor'),
            self::getContents('/sys/devices/virtual/dmi/id/product_name'),
        );
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
            $filePosixUser = null !== $file['uid'] ? \posix_getpwuid($file['uid']) : false;
            $files[] = new Samba\File(
                (int) $file['pid'],
                false !== $filePosixUser ? $filePosixUser['name'] : (string) $file['uid'],
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
