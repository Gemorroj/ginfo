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
use Symfony\Component\Process\Process;

/**
 * Get info on Windows systems
 * Written and maintained by Oliver Kuckertz (mologie).
 * Modify by Gemorroj
 */
class Windows extends OS
{
    private $infoCache = [];

    /**
     * Return a list of things to hide from view..
     *
     * @return array
     */
    public function getContains()
    {
        return [
            'drives_rw_stats' => false,
            'nic_port_speed' => false,
        ];
    }

    /**
     * @param string $name
     * @return array
     */
    private function getInfo($name)
    {
        if (isset($this->infoCache[$name])) {
            return $this->infoCache[$name];
        }

        $powershellDirectory = \getenv('SystemRoot') . '\\System32\\WindowsPowerShell\\v1.0';
        if (!is_dir($powershellDirectory)) {
            $powershellDirectory = null;
        }


        $process = new Process('chcp 65001 | powershell -file ' . __DIR__ . '/../../bin/windows/' . $name . '.ps1', $powershellDirectory);
        $process->mustRun();

        $this->infoCache[$name] = \json_decode($process->getOutput(), true);

        return $this->infoCache[$name];
    }

    /**
     * getOS.
     *
     * @return string current windows version
     */
    public function getOS()
    {
        $info = $this->getInfo('OperatingSystem');
        return $info['Caption'];
    }

    /**
     * getKernel.
     *
     * @return string kernel version
     */
    public function getKernel()
    {
        $info = $this->getInfo('OperatingSystem');
        return $info['Version'] . ' Build ' . $info['BuildNumber'];
    }

    /**
     * getHostName.
     *
     * @return string the host name
     */
    public function getHostName()
    {
        $info = $this->getInfo('OperatingSystem');
        return $info['CSName'];
    }

    /**
     * getRam.
     *
     * @return array the memory information
     */
    public function getRam()
    {
        $info = $this->getInfo('OperatingSystem');

        return array(
            'type' => 'Physical',
            'total' => $info['TotalVisibleMemorySize'],
            'free' => $info['FreePhysicalMemory'],
        );
    }

    /**
     * getCPU.
     *
     * @return array of cpu info
     */
    public function getCPU()
    {
        $cpus = [];
        $cpuInfo = $this->getInfo('Processor');
        $cpuDatas = $this->getInfo('PerfFormattedData_PerfOS_Processor');

        foreach ($cpuDatas as $cpuData) {
            $cpus[] = array(
                'Caption' => $cpuInfo['Caption'],
                'Model' => $cpuInfo['Name'],
                'Vendor' => $cpuInfo['Manufacturer'],
                'MHz' => $cpuInfo['CurrentClockSpeed'],
                'LoadPercentage' => $cpuData['PercentProcessorTime'],
            );
        }

        return $cpus;
    }

    /**
     * getUpTime.
     *
     * @return array uptime
     */
    public function getUpTime()
    {
        $info = $this->getInfo('OperatingSystem');

        // custom windows date format ¯\_(ツ)_/¯
        list($dateTime, $operand, $modifyMinutes) = preg_split('/([\+\-])+/', $info['LastBootUpTime'], -1, PREG_SPLIT_DELIM_CAPTURE);
        $modifyHours = ($modifyMinutes / 60 * 100);

        $booted = \DateTime::createFromFormat('YmdHis.u'.$operand.'O', $dateTime.$operand.$modifyHours, new \DateTimeZone('GMT'));

        return array(
            'text' => Common::secondsConvert(time() - $booted->getTimestamp()),
            'bootedTimestamp' => $booted->getTimestamp(),
        );
    }

    /**
     * getHD.
     *
     * @return array the hard drive info
     */
    public function getHD()
    {
        $drives = [];
        $partitions = [];

        $infoDiskPartition = $this->getInfo('DiskPartition');

        foreach ($infoDiskPartition as $partitionInfo) {
            $partitions[$partitionInfo['DiskIndex']][] = array(
                'size' => $partitionInfo['Size'],
                'name' => $partitionInfo['DeviceID'] . ' (' . $partitionInfo['Type'] . ')',
            );
        }


        $infoDiskDrive = $this->getInfo('DiskDrive');
        if (!isset($infoDiskDrive[0])) { // if onу drive convert to many drives
            $infoDiskDrive = [$infoDiskDrive];
        }

        foreach ($infoDiskDrive as $driveInfo) {
            $drives[] = array(
                'name' => $driveInfo['Caption'],
                'vendor' => explode(' ', $driveInfo['Caption'], 1)[0],
                'device' => $driveInfo['DeviceID'],
                'reads' => false,
                'writes' => false,
                'size' => $driveInfo['Size'],
                'partitions' => array_key_exists($driveInfo['Index'], $partitions) && is_array($partitions[$driveInfo['Index']]) ? $partitions[$driveInfo['Index']] : null,
            );
        }

        return $drives;
    }

    /**
     * getMounts.
     *
     * @return array the mounted the file systems
     */
    public function getMounts()
    {
        $volumes = [];

        $info = $this->getInfo('Volume');

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


            $a = array(
                'device' => false,
                'label' => $volume['Label'],
                'devtype' => null,
                'mount' => $volume['Caption'], // bug \
                'type' => $volume['FileSystem'],
                'size' => $volume['Capacity'],
                'used' => $volume['Capacity'] - $volume['FreeSpace'],
                'free' => $volume['FreeSpace'],
                'free_percent' => 0,
                'used_percent' => 0,
                'options' => $options,
            );

            switch ($volume['DriveType']) {
                case 2:
                    $a['devtype'] = 'Removable drive';
                    break;
                case 3:
                    $a['devtype'] = 'Fixed drive';
                    break;
                case 4:
                    $a['devtype'] = 'Remote drive';
                    break;
                case 5:
                    $a['devtype'] = 'CD-ROM';
                    break;
                case 6:
                    $a['devtype'] = 'RAM disk';
                    break;
                default:
                    $a['devtype'] = 'Unknown';
                    break;
            }

            if ($volume['Capacity'] != 0) {
                $a['free_percent'] = round($volume['FreeSpace'] / $volume['Capacity'], 2) * 100;
                $a['used_percent'] = round(($volume['Capacity'] - $volume['FreeSpace']) / $volume['Capacity'], 2) * 100;
            }

            $volumes[] = $a;
        }

        return $volumes;
    }

    /**
     * getDevs.
     *
     * @return array of devices
     */
    public function getDevs()
    {
        $devs = [];

        $info = $this->getInfo('PnPEntity');

        foreach ($info as $pnpDev) {
            $type = explode('\\', $pnpDev['DeviceID'], 2)[0];
            if (($type != 'USB' && $type != 'PCI') || (empty($pnpDev['Caption']) || mb_substr($pnpDev['Manufacturer'], 0, 1) == '(')) {
                continue;
            }

            $devs[] = array(
                'vendor' => $pnpDev['Manufacturer'],
                'device' => $pnpDev['Caption'],
                'type' => $type,
            );
        }

        return $devs;
    }

    /**
     * getLoad.
     *
     * @return int
     */
    public function getCPUUsage()
    {
        $cpuInfo = $this->getInfo('Processor');

        return $cpuInfo['LoadPercentage'];
    }

    /**
     * getNet.
     *
     * @return array of network devices
     */
    public function getNet()
    {
        $return = [];

        $perfRawData = $this->getInfo('PerfRawData_Tcpip_NetworkInterface');
        $perfRawData = isset($perfRawData[0]) ? $perfRawData : [$perfRawData]; // if one NetworkInterface convert to many NetworkInterfaces
        $networkAdapters = $this->getInfo('NetworkAdapter');

        foreach ($networkAdapters as $net) {
            $return[$net['Name']] = array(
                'recieved' => array(
                    'bytes' => 0,
                    'errors' => 0,
                    'packets' => 0,
                ),
                'sent' => array(
                    'bytes' => 0,
                    'errors' => 0,
                    'packets' => 0,
                ),
                'state' => 0,
                'type' => $net['AdapterType'],
            );
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

            $canonName = preg_replace('/[^A-Za-z0-9- ]/', '_', $net['Name']);
            $isatapName = 'isatap.' . $net['GUID'];


            foreach ($perfRawData as $netSpeed) {
                if ($netSpeed['Name'] === $canonName || $netSpeed['Name'] === $isatapName) {
                    $return[$net['Name']]['recieved'] = array(
                        'bytes' => (int)$netSpeed['BytesReceivedPersec'],
                        'errors' => (int)$netSpeed['PacketsReceivedErrors'],
                        'packets' => (int)$netSpeed['PacketsReceivedPersec'],
                    );
                    $return[$net['Name']]['sent'] = array(
                        'bytes' => (int)$netSpeed['BytesSentPersec'],
                        'errors' => 0,
                        'packets' => (int)$netSpeed['PacketsSentPersec'],
                    );
                }
            }
        }

        return $return;
    }

    /**
     * getWifi.
     *
     * @return array of wifi devices
     */
    public function getWifi()
    {
        return [];
    }

    /**
     * getSoundCards.
     *
     * @return array of soundcards
     */
    public function getSoundCards()
    {
        $cards = [];

        $info = $this->getInfo('SoundDevice');
        if (!isset($info[0])) {
            $info = [$info]; // if one SoundDevice convert to many SoundDevices
        }

        foreach ($info as $key => $card) {
            $cards[] = array(
                'number' => $key,
                'vendor' => $card['Manufacturer'],
                'card' => $card['Caption'],
            );
        }

        return $cards;
    }

    /**
     * getProcessStats.
     *
     * @return array of process stats
     */
    public function getProcessStats()
    {
        $result = array(
            'exists' => true,
            'proc_total' => 0,
            'threads' => 0,
        );

        $info = $this->getInfo('Process');
        foreach ($info as $proc) {
            $result['threads'] += $proc['ThreadCount'];
            ++$result['proc_total'];
        }

        return $result;
    }

    /**
     * getServices.
     *
     * @return array the services
     */
    public function getServices()
    {
        return []; // TODO
    }

    /**
     * getCPUArchitecture.
     *
     * @return string the arch and bits
     */
    public function getCPUArchitecture()
    {
        $info = $this->getInfo('Processor');

        switch ($info['Architecture']) {
            case '0':
                return 'x86';
            case '1':
                return 'MIPS';
            case '2':
                return 'Alpha';
            case '3':
                return 'PowerPC';
            case '6':
                return 'Itanium-based systems';
            case '9':
                return 'x64';
        }

        return 'Unknown';
    }

    /**
     * @return string
     */
    public function getModel()
    {
        $info = $this->getInfo('ComputerSystem');

        return $info['Manufacturer'] . ' (' . $info['Model'] . ')';
    }
}
