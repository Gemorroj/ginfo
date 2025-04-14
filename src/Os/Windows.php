<?php

namespace Ginfo\Os;

use Ginfo\Info\Cpu;
use Ginfo\Info\Disk\Drive;
use Ginfo\Info\Disk\Mount;
use Ginfo\Info\Memory;
use Ginfo\Info\Network;
use Ginfo\Info\Pci;
use Ginfo\Info\Process;
use Ginfo\Info\Samba;
use Ginfo\Info\Selinux;
use Ginfo\Info\Service;
use Ginfo\Info\SoundCard;
use Ginfo\Info\Ups;
use Ginfo\Info\Usb;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Get info on Windows systems.
 */
class Windows implements OsInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $infoCache = [];
    private ?string $powershellDirectory = null;

    public function __construct()
    {
        $powershellDirectory = \getenv('SystemRoot').'\\System32\\WindowsPowerShell\\v1.0';
        if (\is_dir($powershellDirectory)) {
            $this->powershellDirectory = $powershellDirectory;
        }
    }

    /**
     * @return string the arch OS
     */
    public function getArchitecture(): string
    {
        return \php_uname('m');
    }

    /**
     * @return string the OS' hostname A few OS classes override this
     */
    public function getHostName(): string
    {
        return \php_uname('n');
    }

    protected function getInfo(string $name): ?array
    {
        if (!$this->hasInInfoCache($name)) {
            $process = SymfonyProcess::fromShellCommandline('chcp 65001 | powershell -file "!FILE!"', $this->getPowershellDirectory());
            $process->run(null, ['FILE' => __DIR__.'/../../bin/windows/'.$name.'.ps1']);

            if (!$process->isSuccessful()) {
                return null;
            }

            try {
                $result = \json_decode($process->getOutput(), true, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return null;
            }

            $this->addToInfoCache($name, \is_scalar($result) ? [$result] : $result);
        }

        return $this->getFromInfoCache($name);
    }

    public function getLoggedUsers(): ?array
    {
        return $this->getInfo('LoggedOnUser');
    }

    public function getOsName(): string
    {
        $info = $this->getInfo('OperatingSystem');
        if (isset($info['Caption'])) {
            return $info['Caption'];
        }

        return \PHP_OS;
    }

    public function getKernel(): string
    {
        $info = $this->getInfo('OperatingSystem');
        if (isset($info['Version'], $info['BuildNumber'])) {
            return $info['Version'].' Build '.$info['BuildNumber'];
        }

        return \php_uname('r');
    }

    public function getMemory(): ?Memory
    {
        $info = $this->getInfo('OperatingSystem');
        if (null === $info) {
            return null;
        }

        // todo: more swap info
        return new Memory(
            $info['TotalVisibleMemorySize'] * 1000,
            ($info['TotalVisibleMemorySize'] - $info['FreePhysicalMemory']) * 1000,
            $info['FreePhysicalMemory'] * 1000,
            swapTotal: $info['TotalSwapSpaceSize'] * 1000
        );
    }

    public function getCpu(): ?Cpu
    {
        $cpuInfo = $this->getInfo('Processor');
        if (null === $cpuInfo) {
            return null;
        }
        $cpuInfo = isset($cpuInfo[0]) ? $cpuInfo : [$cpuInfo]; // if one processor convert to many processors

        $cores = 0;
        $virtual = 0;
        $processors = [];
        foreach ($cpuInfo as $cpu) {
            $cores += $cpu['NumberOfCores'];
            $virtual += $cpu['NumberOfLogicalProcessors'];

            $architecture = match ($cpu['Architecture']) {
                0 => 'x86',
                1 => 'MIPS',
                2 => 'Alpha',
                3 => 'PowerPC',
                5 => 'ARM',
                6 => 'ia64',
                9 => 'x64',
                12 => 'ARM64',
                default => null,
            };

            $processors[] = new Cpu\Processor(
                $cpu['Name'],
                $cpu['CurrentClockSpeed'],
                $cpu['L2CacheSize'],
                null, // todo
                $architecture,
            );
        }

        return new Cpu(
            \count($cpuInfo),
            $cores,
            $virtual,
            $cores < $virtual,
            $processors
        );
    }

    public function getLoad(): ?array
    {
        return null; // todo
    }

    public function getUptime(): ?float
    {
        $info = $this->getInfo('OperatingSystem');
        if (null === $info) {
            return null;
        }

        // custom windows date format ¯\_(ツ)_/¯
        [$dateTime, $operand, $modifyMinutes] = \preg_split('/([\+\-])+/', $info['LastBootUpTime'], -1, \PREG_SPLIT_DELIM_CAPTURE);
        $modifyHours = ($modifyMinutes / 60 * 100);

        $booted = \DateTimeImmutable::createFromFormat('YmdHis.uO', $dateTime.$operand.$modifyHours, new \DateTimeZone('GMT'));

        return \time() - $booted->getTimestamp();
    }

    public function getDrives(): ?array
    {
        $infoDiskPartition = $this->getInfo('DiskPartition');
        if (null === $infoDiskPartition) {
            return null;
        }

        $infoDiskDrive = $this->getInfo('DiskDrive');
        if (null === $infoDiskDrive) {
            return null;
        }
        $infoDiskDrive = isset($infoDiskDrive[0]) ? $infoDiskDrive : [$infoDiskDrive]; // if one drive convert to many drives

        $drives = [];
        $partitions = [];

        foreach ($infoDiskPartition as $partitionInfo) {
            $partitions[$partitionInfo['DiskIndex']][] = new Drive\Partition(
                $partitionInfo['Size'],
                $partitionInfo['DeviceID'].' ('.$partitionInfo['Type'].')'
            );
        }

        foreach ($infoDiskDrive as $driveInfo) {
            $namePartition = $driveInfo['Index'];
            $p = \array_key_exists($namePartition, $partitions) && \is_array($partitions[$namePartition]) ? $partitions[$namePartition] : [];

            $drives[] = new Drive(
                $driveInfo['Caption'],
                $driveInfo['DeviceID'],
                $driveInfo['Size'],
                \str_contains($driveInfo['Caption'], ' ') ? \explode(' ', $driveInfo['Caption'], 2)[0] : null,
                null,
                null,
                $p,
            );
        }

        return $drives;
    }

    public function getMounts(): ?array
    {
        $info = $this->getInfo('Volume');
        if (null === $info) {
            return null;
        }

        $volumes = [];
        foreach ($info as $volume) {
            $device = $volume['Label'];
            switch ($volume['DriveType']) {
                case 2:
                    $device .= ' (Removable drive)';
                    break;
                case 3:
                    $device .= ' (Fixed drive)';
                    break;
                case 4:
                    $device .= ' (Remote drive)';
                    break;
                case 5:
                    $device .= ' (CD-ROM)';
                    break;
                case 6:
                    $device .= ' (RAM disk)';
                    break;
            }

            $options = [];
            if ($volume['Automount']) {
                $options[] = 'automount';
            }
            if ($volume['BootVolume']) {
                $options[] = 'boot';
            }
            if ($volume['IndexingEnabled']) {
                $options[] = 'indexed';
            }
            if ($volume['Compressed']) {
                $options[] = 'compressed';
            }

            $volumes[] = new Mount(
                $device,
                $volume['Caption'],
                $volume['FileSystem'],
                $volume['Capacity'],
                $volume['Capacity'] - $volume['FreeSpace'],
                $volume['FreeSpace'],
                $volume['Capacity'] > 0 ? \round($volume['FreeSpace'] / $volume['Capacity'], 2) * 100 : null,
                $volume['Capacity'] > 0 ? \round(($volume['Capacity'] - $volume['FreeSpace']) / $volume['Capacity'], 2) * 100 : null,
                $options
            );
        }

        return $volumes;
    }

    public function getRaids(): ?array
    {
        return null; // todo
    }

    public function getPci(): ?array
    {
        $info = $this->getInfo('PnPEntity');
        if (null === $info) {
            return null;
        }

        $devs = [];
        foreach ($info as $pnpDev) {
            $type = \explode('\\', $pnpDev['DeviceID'], 2)[0];
            if (('PCI' !== $type) || (empty($pnpDev['Caption']) || \str_starts_with($pnpDev['Manufacturer'], '('))) {
                continue;
            }

            $devs[] = new Pci($pnpDev['Manufacturer'], $pnpDev['Caption']);
        }

        return $devs;
    }

    public function getUsb(): ?array
    {
        $info = $this->getInfo('PnPEntity');
        if (null === $info) {
            return null;
        }

        $devs = [];
        foreach ($info as $pnpDev) {
            $type = \explode('\\', $pnpDev['DeviceID'], 2)[0];
            if (('USB' !== $type) || (empty($pnpDev['Caption']) || \str_starts_with($pnpDev['Manufacturer'], '('))) {
                continue;
            }

            $devs[] = new Usb($pnpDev['Manufacturer'], $pnpDev['Caption']);
        }

        return $devs;
    }

    public function getNetwork(): ?array
    {
        $perfRawData = $this->getInfo('PerfRawData_Tcpip_NetworkInterface');
        if (null === $perfRawData) {
            return null;
        }
        $perfRawData = isset($perfRawData[0]) ? $perfRawData : [$perfRawData]; // if one NetworkInterface convert to many NetworkInterfaces
        $networkAdapters = $this->getInfo('NetworkAdapter');
        if (null === $networkAdapters) {
            return null;
        }
        $networkAdapters = isset($networkAdapters[0]) ? $networkAdapters : [$networkAdapters]; // if one NetworkAdapter convert to many NetworkAdapters

        $return = [];
        foreach ($networkAdapters as $net) {
            $nameNormalizer = static function (string $name): string {
                return \preg_replace('/[^A-Za-z0-9- ]/', '_', $name);
            };

            $canonName = $nameNormalizer($net['Name']);
            $isatapName = 'isatap.'.$net['GUID'];

            foreach ($perfRawData as $netSpeed) {
                if ($netSpeed['Name'] === $isatapName || $nameNormalizer($netSpeed['Name']) === $canonName) {
                    $statsReceived = new Network\Stats($netSpeed['BytesReceivedPersec'], $netSpeed['PacketsReceivedErrors'], $netSpeed['PacketsReceivedPersec']);
                    $statsSent = new Network\Stats($netSpeed['BytesSentPersec'], $netSpeed['PacketsOutboundErrors'], $netSpeed['PacketsSentPersec']);
                    // break;
                }
            }

            $state = match ($net['NetConnectionStatus']) {
                0 => 'down',
                1 => 'connecting',
                2 => 'up',
                3 => 'disconnecting',
                4 => 'down', // MSDN 'Hardware not present'
                5 => 'hardware disabled',
                6 => 'hardware malfunction',
                7 => 'media disconnected',
                8 => 'authenticating',
                9 => 'authentication succeeded',
                10 => 'authentication failed',
                11 => 'invalid address',
                12 => 'credentials required',
                default => null,
            };

            $return[] = new Network(
                $net['Name'],
                $net['Speed'],
                $net['AdapterType'],
                $state,
                $statsReceived,
                $statsSent,
            );
        }

        return $return;
    }

    public function getBattery(): ?array
    {
        return null; // todo
    }

    public function getSensors(): ?array
    {
        return null; // todo
    }

    public function getSoundCards(): ?array
    {
        $info = $this->getInfo('SoundDevice');
        if (null === $info) {
            return null;
        }
        $info = isset($info[0]) ? $info : [$info]; // if one SoundDevice convert to many SoundDevices

        $cards = [];
        foreach ($info as $card) {
            $cards[] = new SoundCard($card['Manufacturer'], $card['Caption']);
        }

        return $cards;
    }

    public function getProcesses(): ?array
    {
        $info = $this->getInfo('Process');
        if (null === $info) {
            return null;
        }

        $result = [];
        foreach ($info as $proc) {
            $state = match ($proc['ExecutionState']) {
                1 => 'other',
                2 => 'ready',
                3 => 'running',
                4 => 'blocked',
                5 => 'suspended blocked',
                6 => 'suspended ready',
                7 => 'terminated',
                8 => 'stopped',
                9 => 'growing',
                default => null,
            };

            $result[] = new Process(
                $proc['Name'],
                $proc['ProcessId'],
                $proc['CommandLine'],
                $proc['ThreadCount'],
                $state,
                $proc['VirtualSize'],
                $proc['PeakVirtualSize'],
                $proc['Owner'],
                null, // todo
                null, // todo
            );
        }

        return $result;
    }

    public function getServices(): ?array
    {
        $services = $this->getInfo('Service');
        if (null === $services) {
            return null;
        }

        $return = [];
        foreach ($services as $service) {
            $return[] = new Service($service['Name'], $service['DisplayName'], true, $service['Started'], $service['State']);
        }

        return $return;
    }

    public function getModel(): ?string
    {
        $info = $this->getInfo('ComputerSystem');
        if (null === $info) {
            return null;
        }

        return $info['Manufacturer'].' ('.$info['Model'].')';
    }

    public function getVirtualization(): ?string
    {
        return null; // TODO
    }

    public function getUps(): ?Ups
    {
        return null; // todo
    }

    public function getPrinters(): ?array
    {
        return null; // todo
    }

    public function getSamba(): ?Samba
    {
        return null; // todo
    }

    public function getSelinux(): ?Selinux
    {
        return null;
    }

    protected function setPowershellDirectory(?string $path): self
    {
        $this->powershellDirectory = $path;

        return $this;
    }

    protected function getPowershellDirectory(): ?string
    {
        return $this->powershellDirectory;
    }

    protected function addToInfoCache(string $name, mixed $value): self
    {
        $this->infoCache[$name] = $value;

        return $this;
    }

    protected function hasInInfoCache(string $name): bool
    {
        return \array_key_exists($name, $this->infoCache);
    }

    protected function getFromInfoCache(string $name, mixed $defaultValue = null): mixed
    {
        return $this->hasInInfoCache($name) ? $this->infoCache[$name] : $defaultValue;
    }

    protected function removeFromInfoCache(string $name): self
    {
        if ($this->hasInInfoCache($name)) {
            unset($this->infoCache[$name]);
        }

        return $this;
    }

    protected function cleanInfoCache(): self
    {
        $this->infoCache = [];

        return $this;
    }
}
