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

use Linfo\Exceptions\FatalException;
use Linfo\Info\Cpu;
use Symfony\Component\Process\Process;

/**
 * Get info on Windows systems
 * Written and maintained by Oliver Kuckertz (mologie).
 */
class Windows extends OS
{
    private $process;
    private $infoCache = [];

    /**
     * Windows constructor.
     * @throws FatalException
     */
    public function __construct()
    {
        try {
            $powershellDirectory = \getenv('SystemRoot') . '\\System32\\WindowsPowerShell\\v1.0';
            if (!\is_dir($powershellDirectory)) {
                $powershellDirectory = null;
            }

            $this->process = new Process(null, $powershellDirectory);
        } catch (\Exception $e) {
            throw new FatalException($e->getMessage());
        }
    }

    /**
     * @param string $name
     * @return array|null
     */
    private function getInfo(string $name) : ?array
    {
        if (isset($this->infoCache[$name])) {
            return $this->infoCache[$name];
        }

        $process = $this->process->setCommandLine('chcp 65001 | powershell -file ' . __DIR__ . '/../../bin/windows/' . $name . '.ps1');
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $this->infoCache[$name] = \json_decode($process->getOutput(), true);

        return $this->infoCache[$name];
    }


    public function getLoggedUsers() : ?array
    {
        return $this->getInfo('LoggedOnUser');
    }


    public function getOsName() : string
    {
        $info = $this->getInfo('OperatingSystem');
        if (isset($info['Caption'])) {
            return $info['Caption'];
        }

        return \php_uname('s');
    }


    public function getKernel() : string
    {
        $info = $this->getInfo('OperatingSystem');
        if (isset($info['Version']) && isset($info['BuildNumber'])) {
            return $info['Version'] . ' Build ' . $info['BuildNumber'];
        }

        return parent::getKernel();
    }


    public function getMemory() : ?array
    {
        $info = $this->getInfo('OperatingSystem');
        if (null === $info) {
            return null;
        }

        return [
            'memoryTotal' => $info['TotalVisibleMemorySize'],
            'memoryUsed' => $info['TotalVisibleMemorySize'] - $info['FreePhysicalMemory'],
            'memoryFree' => $info['FreePhysicalMemory'],
            'memoryShared' => null, // todo
            'memoryBuffers' => null, // todo
            'memoryCached' => null, // todo

            'swapTotal' => null, // todo
            'swapUsed' => null, // todo
            'swapFree' => null, // todo
        ];
    }


    public function getCpu() : ?Cpu
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
                ->setFlags(null); //todo
        }

        return (new Cpu())
            ->setPhysical(\count($cpuInfo))
            ->setVirtual($virtual)
            ->setCores($cores)
            ->setHyperThreading($cores < $virtual)
            ->setProcessors($processors);
    }

    public function getLoad() : ?array
    {
        return null; //todo
    }

    public function getUptime() : ?int
    {
        $info = $this->getInfo('OperatingSystem');
        if (null === $info) {
            return null;
        }

        // custom windows date format ¯\_(ツ)_/¯
        list($dateTime, $operand, $modifyMinutes) = \preg_split('/([\+\-])+/', $info['LastBootUpTime'], -1, PREG_SPLIT_DELIM_CAPTURE);
        $modifyHours = ($modifyMinutes / 60 * 100);

        $booted = \DateTime::createFromFormat('YmdHis.u'.$operand.'O', $dateTime.$operand.$modifyHours, new \DateTimeZone('GMT'));

        return \time() - $booted->getTimestamp();
    }


    public function getPartitions() : ?array
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
            $partitions[$partitionInfo['DiskIndex']][] = [
                'size' => $partitionInfo['Size'],
                'name' => $partitionInfo['DeviceID'] . ' (' . $partitionInfo['Type'] . ')',
            ];
        }

        foreach ($infoDiskDrive as $driveInfo) {
            $drives[] = [
                'name' => $driveInfo['Caption'],
                'vendor' => \explode(' ', $driveInfo['Caption'], 1)[0],
                'device' => $driveInfo['DeviceID'],
                'reads' => null, //todo
                'writes' => null, //todo
                'size' => $driveInfo['Size'],
                'partitions' => \array_key_exists($driveInfo['Index'], $partitions) && \is_array($partitions[$driveInfo['Index']]) ? $partitions[$driveInfo['Index']] : null,
            ];
        }

        return $drives;
    }

    public function getMounts() : ?array
    {
        $info = $this->getInfo('Volume');
        if (null === $info) {
            return null;
        }

        $volumes = [];
        foreach ($info as $volume) {
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

            $a = [
                'device' => $volume['Label'],
                'mount' => $volume['Caption'],
                'type' => $volume['FileSystem'],
                'size' => $volume['Capacity'],
                'used' => $volume['Capacity'] - $volume['FreeSpace'],
                'free' => $volume['FreeSpace'],
                'freePercent' => null,
                'usedPercent' => null,
                'options' => $options,
            ];

            switch ($volume['DriveType']) {
                case 2:
                    $a['device'] .= ' (Removable drive)';
                    break;
                case 3:
                    //$a['device'] .= ' (Fixed drive)';
                    break;
                case 4:
                    $a['device'] .= ' (Remote drive)';
                    break;
                case 5:
                    $a['device'] .= ' (CD-ROM)';
                    break;
                case 6:
                    $a['device'] .= ' (RAM disk)';
                    break;
            }

            if ($volume['Capacity'] != 0) {
                $a['freePercent'] = \round($volume['FreeSpace'] / $volume['Capacity'], 2) * 100;
                $a['usedPercent'] = \round(($volume['Capacity'] - $volume['FreeSpace']) / $volume['Capacity'], 2) * 100;
            }

            $volumes[] = $a;
        }

        return $volumes;
    }

    public function getRaid() : ?array
    {
        return null; //todo
    }

    public function getPci() : ?array
    {
        $info = $this->getInfo('PnPEntity');
        if (null === $info) {
            return null;
        }

        $devs = [];
        foreach ($info as $pnpDev) {
            $type = \explode('\\', $pnpDev['DeviceID'], 2)[0];
            if (($type !== 'PCI') || (empty($pnpDev['Caption']) || \mb_substr($pnpDev['Manufacturer'], 0, 1) == '(')) {
                continue;
            }

            $devs[] = [
                'vendor' => $pnpDev['Manufacturer'],
                'device' => $pnpDev['Caption'],
            ];
        }

        return $devs;
    }


    public function getUsb() : ?array
    {
        $info = $this->getInfo('PnPEntity');
        if (null === $info) {
            return null;
        }

        $devs = [];
        foreach ($info as $pnpDev) {
            $type = \explode('\\', $pnpDev['DeviceID'], 2)[0];
            if (($type !== 'USB') || (empty($pnpDev['Caption']) || \mb_substr($pnpDev['Manufacturer'], 0, 1) == '(')) {
                continue;
            }

            $devs[] = [
                'vendor' => $pnpDev['Manufacturer'],
                'device' => $pnpDev['Caption'],
            ];
        }

        return $devs;
    }


    public function getNetwork() : ?array
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

        $return = [];
        foreach ($networkAdapters as $net) {
            $return[$net['Name']] = [
                'recieved' => [
                    'bytes' => 0,
                    'errors' => 0,
                    'packets' => 0,
                ],
                'sent' => [
                    'bytes' => 0,
                    'errors' => 0,
                    'packets' => 0,
                ],
                'state' => null,
                'type' => $net['AdapterType'],
                'portSpeed' => null, //todo
            ];

            switch ($net['NetConnectionStatus']) {
                case 0:
                    $return[$net['Name']]['state'] = 'down';
                    break;
                case 1:
                    $return[$net['Name']]['state'] = 'Connecting';
                    break;
                case 2:
                    $return[$net['Name']]['state'] = 'up';
                    break;
                case 3:
                    $return[$net['Name']]['state'] = 'Disconnecting';
                    break;
                case 4:
                    $return[$net['Name']]['state'] = 'down'; // MSDN 'Hardware not present'
                    break;
                case 5:
                    $return[$net['Name']]['state'] = 'Hardware disabled';
                    break;
                case 6:
                    $return[$net['Name']]['state'] = 'Hardware malfunction';
                    break;
                case 7:
                    $return[$net['Name']]['state'] = 'Media disconnected';
                    break;
                case 8:
                    $return[$net['Name']]['state'] = 'Authenticating';
                    break;
                case 9:
                    $return[$net['Name']]['state'] = 'Authentication succeeded';
                    break;
                case 10:
                    $return[$net['Name']]['state'] = 'Authentication failed';
                    break;
                case 11:
                    $return[$net['Name']]['state'] = 'Invalid address';
                    break;
                case 12:
                    $return[$net['Name']]['state'] = 'Credentials required';
                    break;
                default:
                    $return[$net['Name']]['state'] = 'unknown';
                    break;
            }

            $canonName = \preg_replace('/[^A-Za-z0-9- ]/', '_', $net['Name']);
            $isatapName = 'isatap.' . $net['GUID'];


            foreach ($perfRawData as $netSpeed) {
                if ($netSpeed['Name'] === $canonName || $netSpeed['Name'] === $isatapName) {
                    $return[$net['Name']]['recieved'] = [
                        'bytes' => (int)$netSpeed['BytesReceivedPersec'],
                        'errors' => (int)$netSpeed['PacketsReceivedErrors'],
                        'packets' => (int)$netSpeed['PacketsReceivedPersec'],
                    ];
                    $return[$net['Name']]['sent'] = [
                        'bytes' => (int)$netSpeed['BytesSentPersec'],
                        'errors' => 0,
                        'packets' => (int)$netSpeed['PacketsSentPersec'],
                    ];
                }
            }
        }

        return $return;
    }


    public function getWifi() : ?array
    {
        return null; // todo
    }

    public function getBattery() : ?array
    {
        return null; //todo
    }

    public function getTemps() : ?array
    {
        return null; //todo
    }

    public function getSoundCards() : ?array
    {
        $info = $this->getInfo('SoundDevice');
        if (null === $info) {
            return null;
        }
        $info = isset($info[0]) ? $info : [$info]; // if one SoundDevice convert to many SoundDevices

        $cards = [];
        foreach ($info as $card) {
            $cards[] = [
                'vendor' => $card['Manufacturer'],
                'card' => $card['Caption'],
            ];
        }

        return $cards;
    }


    public function getProcesses() : ?array
    {
        $info = $this->getInfo('Process');
        if (null === $info) {
            return null;
        }

        $displayState = function ($numberState) {
            switch ($numberState) {
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
        };

        $result = [];
        foreach ($info as $proc) {
            $result[] = [
                'name' => $proc['Name'],
                'commandLine' => $proc['CommandLine'],
                'threads' => $proc['ThreadCount'],
                'state' => $displayState($proc['ExecutionState']),
                'memory' => $proc['VirtualSize'],
                'peakMemory' => $proc['PeakVirtualSize'],
                'pid' => $proc['ProcessId'],
                'user' => $proc['Owner'],
            ];
        }

        return $result;
    }

    public function getServices() : ?array
    {
        $services = $this->getInfo('Service');
        if (null === $services) {
            return null;
        }

        $return = [];
        foreach ($services as $service) {
            $return[] = [
                'name' => $service['Name'],
                'load' => null,
                'active' => null,
                'sub' => $service['State'],
                'description' => $service['DisplayName'],
            ];
        }

        return $return;
    }


    public function getModel() : ?string
    {
        $info = $this->getInfo('ComputerSystem');
        if (null === $info) {
            return null;
        }

        return $info['Manufacturer'] . ' (' . $info['Model'] . ')';
    }

    public function getVirtualization() : ?string
    {
        return null; // TODO
    }

    public function getUps() : ?array
    {
        return null; //todo
    }

    public function getPrinters() : ?array
    {
        return null; //todo
    }

    public function getSamba() : ?array
    {
        return null; //todo
    }

    public function getSelinux() : ?array
    {
        return null;
    }
}
