<?php

namespace Ginfo\OS;

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
 * Get info on Windows systems
 * Written and maintained by Oliver Kuckertz (mologie).
 */
class Windows extends OS
{
    private $infoCache = [];
    private $powershellDirectory;

    public function __construct()
    {
        $powershellDirectory = \getenv('SystemRoot').'\\System32\\WindowsPowerShell\\v1.0';
        if (\is_dir($powershellDirectory)) {
            $this->setPowershellDirectory($powershellDirectory);
        }
    }

    protected function getInfo(string $name): ?array
    {
        if (!$this->hasInInfoCache($name)) {
            $process = SymfonyProcess::fromShellCommandline('chcp 65001 | powershell -file "!FILE!"', $this->getPowershellDirectory());
            $process->run(null, ['FILE' => __DIR__.'/../../bin/windows/'.$name.'.ps1']);

            if (!$process->isSuccessful()) {
                return null;
            }

            $result = \json_decode($process->getOutput(), true);

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

        return parent::getKernel();
    }

    public function getMemory(): ?Memory
    {
        $info = $this->getInfo('OperatingSystem');
        if (null === $info) {
            return null;
        }

        // todo: more swap info
        return (new Memory())
            ->setSwapTotal($info['TotalSwapSpaceSize'] * 1000)
            ->setTotal($info['TotalVisibleMemorySize'] * 1000)
            ->setFree($info['FreePhysicalMemory'] * 1000)
            ->setUsed(($info['TotalVisibleMemorySize'] - $info['FreePhysicalMemory']) * 1000);
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

            $processors[] = (new Cpu\Processor())
                ->setModel($cpu['Name'])
                ->setSpeed($cpu['CurrentClockSpeed'])
                ->setL2Cache($cpu['L2CacheSize'])
                ->setArchitecture((static function () use ($cpu): ?string {
                    switch ($cpu['Architecture']) {
                        case 0:
                            return 'x86';
                            break;
                        case 1:
                            return 'MIPS';
                            break;
                        case 2:
                            return 'Alpha';
                            break;
                        case 3:
                            return 'PowerPC';
                            break;
                        case 5:
                            return 'ARM';
                            break;
                        case 6:
                            return 'ia64';
                            break;
                        case 9:
                            return 'x64';
                            break;
                    }

                    return null;
                })())
                ->setFlags(null); //todo
        }

        return (new Cpu())
            ->setPhysical(\count($cpuInfo))
            ->setVirtual($virtual)
            ->setCores($cores)
            ->setHyperThreading($cores < $virtual)
            ->setProcessors($processors);
    }

    public function getLoad(): ?array
    {
        return null; //todo
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

        $booted = \DateTime::createFromFormat('YmdHis.uO', $dateTime.$operand.$modifyHours, new \DateTimeZone('GMT'));

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
            $partitions[$partitionInfo['DiskIndex']][] = (new Drive\Partition())
                ->setSize($partitionInfo['Size'])
                ->setName($partitionInfo['DeviceID'].' ('.$partitionInfo['Type'].')');
        }

        foreach ($infoDiskDrive as $driveInfo) {
            $drives[] = (new Drive())
                ->setSize($driveInfo['Size'])
                ->setDevice($driveInfo['DeviceID'])
                ->setPartitions((static function (string $namePartition) use ($partitions): ?array {
                    return \array_key_exists($namePartition, $partitions) && \is_array($partitions[$namePartition]) ? $partitions[$namePartition] : null;
                })($driveInfo['Index']))
                ->setName($driveInfo['Caption'])
                ->setReads(null)
                ->setVendor(false !== \mb_strpos($driveInfo['Caption'], ' ') ? \explode(' ', $driveInfo['Caption'], 2)[0] : null)
                ->setWrites(null);
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
            $volumes[] = (new Mount())
                ->setSize($volume['Capacity'])
                ->setDevice((static function () use ($volume): string {
                    $name = $volume['Label'];
                    switch ($volume['DriveType']) {
                        case 2:
                            $name .= ' (Removable drive)';
                            break;
                        case 3:
                            $name .= ' (Fixed drive)';
                            break;
                        case 4:
                            $name .= ' (Remote drive)';
                            break;
                        case 5:
                            $name .= ' (CD-ROM)';
                            break;
                        case 6:
                            $name .= ' (RAM disk)';
                            break;
                    }

                    return $name;
                })())
                ->setType($volume['FileSystem'])
                ->setFree($volume['FreeSpace'])
                ->setMount($volume['Caption'])
                ->setOptions((static function () use ($volume): array {
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

                    return $options;
                })())
                ->setUsed($volume['Capacity'] - $volume['FreeSpace'])
                ->setFreePercent($volume['Capacity'] > 0 ? \round($volume['FreeSpace'] / $volume['Capacity'], 2) * 100 : null)
                ->setUsedPercent($volume['Capacity'] > 0 ? \round(($volume['Capacity'] - $volume['FreeSpace']) / $volume['Capacity'], 2) * 100 : null);
        }

        return $volumes;
    }

    public function getRaids(): ?array
    {
        return null; //todo
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
            if (('PCI' !== $type) || (empty($pnpDev['Caption']) || 0 === \mb_strpos($pnpDev['Manufacturer'], '('))) {
                continue;
            }

            $devs[] = (new Pci())
                ->setVendor($pnpDev['Manufacturer'])
                ->setName($pnpDev['Caption']);
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
            if (('USB' !== $type) || (empty($pnpDev['Caption']) || 0 === \mb_strpos($pnpDev['Manufacturer'], '('))) {
                continue;
            }

            $devs[] = (new Usb())
                ->setVendor($pnpDev['Manufacturer'])
                ->setName($pnpDev['Caption']);
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
            $tmp = (new Network())
                ->setName($net['Name'])
                ->setSpeed($net['Speed'])
                ->setType($net['AdapterType'])
                ->setState((static function () use ($net): ?string {
                    switch ($net['NetConnectionStatus']) {
                        case 0:
                            return 'down';
                            break;
                        case 1:
                            return 'connecting';
                            break;
                        case 2:
                            return 'up';
                            break;
                        case 3:
                            return 'disconnecting';
                            break;
                        case 4:
                            return 'down'; // MSDN 'Hardware not present'
                            break;
                        case 5:
                            return 'hardware disabled';
                            break;
                        case 6:
                            return 'hardware malfunction';
                            break;
                        case 7:
                            return 'media disconnected';
                            break;
                        case 8:
                            return 'authenticating';
                            break;
                        case 9:
                            return 'authentication succeeded';
                            break;
                        case 10:
                            return 'authentication failed';
                            break;
                        case 11:
                            return 'invalid address';
                            break;
                        case 12:
                            return 'credentials required';
                            break;
                    }

                    return null;
                })());

            $nameNormalizer = static function (string $name): string {
                return \preg_replace('/[^A-Za-z0-9- ]/', '_', $name);
            };

            $canonName = $nameNormalizer($net['Name']);
            $isatapName = 'isatap.'.$net['GUID'];

            foreach ($perfRawData as $netSpeed) {
                if ($netSpeed['Name'] === $isatapName || $nameNormalizer($netSpeed['Name']) === $canonName) {
                    $tmp->setStatsReceived(
                        (new Network\Stats())
                            ->setBytes($netSpeed['BytesReceivedPersec'])
                            ->setErrors($netSpeed['PacketsReceivedErrors'])
                            ->setPackets($netSpeed['PacketsReceivedPersec'])
                    );
                    $tmp->setStatsSent(
                        (new Network\Stats())
                            ->setBytes($netSpeed['BytesSentPersec'])
                            ->setErrors($netSpeed['PacketsOutboundErrors'])
                            ->setPackets($netSpeed['PacketsSentPersec'])
                    );
                }
            }

            $return[] = $tmp;
        }

        return $return;
    }

    public function getBattery(): ?array
    {
        return null; //todo
    }

    public function getSensors(): ?array
    {
        return null; //todo
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
            $cards[] = (new SoundCard())
                ->setVendor($card['Manufacturer'])
                ->setName($card['Caption']);
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
            $result[] = (new Process())
                ->setName($proc['Name'])
                ->setCommandLine($proc['CommandLine'])
                ->setThreads($proc['ThreadCount'])
                ->setState((static function () use ($proc): ?string {
                    switch ($proc['ExecutionState']) {
                        case 1:
                            return 'other';
                            break;
                        case 2:
                            return 'ready';
                            break;
                        case 3:
                            return 'running';
                            break;
                        case 4:
                            return 'blocked';
                            break;
                        case 5:
                            return 'suspended blocked';
                            break;
                        case 6:
                            return 'suspended ready';
                            break;
                        case 7:
                            return 'terminated';
                            break;
                        case 8:
                            return 'stopped';
                            break;
                        case 9:
                            return 'growing';
                            break;
                    }

                    return null;
                })())
                ->setMemory($proc['VirtualSize'])
                ->setPeakMemory($proc['PeakVirtualSize'])
                ->setPid($proc['ProcessId'])
                ->setUser($proc['Owner'])
                ->setIoRead(null) //todo
                ->setIoWrite(null); // todo
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
            $return[] = (new Service())
                ->setName($service['Name'])
                ->setDescription($service['DisplayName'])
                ->setLoaded(true)
                ->setStarted($service['Started'])
                ->setState($service['State']);
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
        return null; //todo
    }

    public function getPrinters(): ?array
    {
        return null; //todo
    }

    public function getSamba(): ?Samba
    {
        return null; //todo
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

    protected function addToInfoCache(string $name, $value): self
    {
        $this->infoCache[$name] = $value;

        return $this;
    }

    protected function hasInInfoCache(string $name): bool
    {
        return \array_key_exists($name, $this->infoCache);
    }

    protected function getFromInfoCache(string $name, $defaultValue = null)
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
