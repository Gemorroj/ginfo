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
use Linfo\Parsers\CallExt;

/**
 * Get info on Windows systems
 * Written and maintained by Oliver Kuckertz (mologie).
 * Modify by Gemorroj
 */
class Windows extends OS
{
    private $systemInfo = array();

    /**
     * Windows constructor.
     */
    public function __construct()
    {
        parent::__construct();

        setlocale(LC_ALL, 'English');
        shell_exec('chcp 65001');

        $this->callExt->setSearchPaths([getenv('SystemRoot') . '\\System32\\Wbem', getenv('SystemRoot') . '\\System32']);

        $this->makeSystemInfo();
        //print_r($this->systemInfo);
    }

    /**
     *
     * make array from wmic list data
     * /format:csv is bogus (no quotes commas)
     * @param string $str
     * @return array
     */
    private function parseWmicListData($str)
    {
        $out = array();
        $data = explode("\n", trim($str));

        $i = 0;
        foreach ($data as $row) {
            if (trim($row) === '') {
                $i++;
                continue;
            }

            $parsedRow = explode('=', $row, 2);

            $out[$i][$parsedRow[0]] = trim($parsedRow[1]);

            if (strtolower($out[$i][$parsedRow[0]]) === 'true') {
                $out[$i][$parsedRow[0]] = true;
            } else if (strtolower($out[$i][$parsedRow[0]]) === 'false') {
                $out[$i][$parsedRow[0]] = false;
            }
        }

        return $out;
    }


    /**
     * @return array
     */
    public function getSystemInfo()
    {
        return $this->systemInfo;
    }

    private function makeSystemInfo()
    {
        $systemInfo = explode("\n", trim(shell_exec('chcp 65001 | systeminfo.exe /fo csv'))); //fix cp
        //$systemInfo = explode("\n", trim($this->callExt->exec('systeminfo.exe', '/fo csv')));

        $this->systemInfo = array_combine(
            str_getcsv($systemInfo[0]),
            str_getcsv($systemInfo[1])
        );
    }


    /**
     * Return a list of things to hide from view..
     *
     * @return array
     */
    public function getContains()
    {
        return array(
            'drives_rw_stats' => false,
            'nic_port_speed' => false,
        );
    }

    /**
     * getOS.
     *
     * @return string current windows version
     */
    public function getOS()
    {
        return $this->systemInfo['OS Name'];
    }

    /**
     * getKernel.
     *
     * @return string kernel version
     */
    public function getKernel()
    {
        return $this->systemInfo['OS Version'];
    }

    /**
     * getHostName.
     *
     * @return string the host name
     */
    public function getHostName()
    {
        return $this->systemInfo['Host Name'];
    }

    /**
     * getRam.
     *
     * @return array the memory information
     */
    public function getRam()
    {
        return array(
            'type' => 'Physical',
            'total' => $this->systemInfo['Total Physical Memory'],
            'free' => $this->systemInfo['Available Physical Memory'],
        );
    }

    /**
     * getCPU.
     *
     * @return array of cpu info
     */
    public function getCPU()
    {
        $cpus = array();

        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'CPU GET /FORMAT:list')) as $cpuInfo) {
            $cpus[] = array(
                'Caption' => $cpuInfo['Caption'],
                'Model' => $cpuInfo['Name'],
                'Vendor' => $cpuInfo['Manufacturer'],
                'MHz' => $cpuInfo['CurrentClockSpeed'],
                'LoadPercentage' => $cpuInfo['LoadPercentage'],
                'Cores' => $cpuInfo['NumberOfCores'],
                //'Threads' => $cpuInfo['ThreadCount'], // Windows 7 - not exists, Windows 10 - exists
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
        $booted = new \DateTime($this->systemInfo['System Boot Time']);

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
        $drives = array();
        $partitions = array();

        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'partition get /FORMAT:list')) as $partitionInfo) {
            $partitions[$partitionInfo['DiskIndex']][] = array(
                'size' => $partitionInfo['Size'],
                'name' => $partitionInfo['DeviceID'] . ' (' . $partitionInfo['Type'] . ')',
            );
        }


        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'diskdrive get /FORMAT:list')) as $driveInfo) {
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
     * getTemps.
     *
     * @return array the temps
     */
    public function getTemps()
    {
        return array(); // TODO
    }

    /**
     * getMounts.
     *
     * @return array the mounted the file systems
     */
    public function getMounts()
    {
        $volumes = array();
        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'volume get /FORMAT:list')) as $volume) {
            $options = array();

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
                'label' => mb_convert_encoding($volume['Label'], 'UTF-8', 'CP866'),
                'devtype' => null,
                'mount' => $volume['Caption'],
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
        $devs = array();

        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'path Win32_PnPEntity get /FORMAT:list')) as $pnpdev) {
            $type = explode('\\', $pnpdev['DeviceID'], 2)[0];
            if (($type != 'USB' && $type != 'PCI') || (empty($pnpdev['Caption']) || mb_substr($pnpdev['Manufacturer'], 0, 1) == '(')) {
                continue;
            }

            $devs[] = array(
                'vendor' => mb_convert_encoding($pnpdev['Manufacturer'], 'UTF-8', 'CP866'),
                'device' => mb_convert_encoding($pnpdev['Caption'], 'UTF-8', 'CP866'),
                'type' => $type,
            );
        }

        return $devs;
    }

    /**
     * getRAID.
     *
     * @return array of raid arrays
     */
    public function getRAID()
    {
        return array();
    }

    /**
     * getLoad.
     *
     * @return array of current system load values
     */
    public function getLoad()
    {
        $load = array();
        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'CPU GET /FORMAT:list')) as $cpu) {
            $load[] = $cpu['LoadPercentage'];
        }

        return array(round(array_sum($load) / count($load), 2));
    }

    /**
     * getNet.
     *
     * @return array of network devices
     */
    public function getNet()
    {
        $return = array();
        $i = 0;

        $perfRawData = $this->parseWmicListData($this->callExt->exec('wmic.exe', 'path Win32_PerfRawData_Tcpip_NetworkInterface GET /FORMAT:list'));

        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'nic GET /FORMAT:list')) as $net) {
            if (!$net['PhysicalAdapter']) {
                continue;
            }

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
     * getBattery.
     *
     * @return array of battery status
     */
    public function getBattery()
    {
        return array(); // TODO
    }

    /**
     * getWifi.
     *
     * @return array of wifi devices
     */
    public function getWifi()
    {
        return array();
    }

    /**
     * getSoundCards.
     *
     * @return array of soundcards
     */
    public function getSoundCards()
    {
        $cards = array();
        $i = 1;
        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'SOUNDDEV GET /FORMAT:list')) as $card) {
            $cards[] = array(
                'number' => $i++,
                'vendor' => mb_convert_encoding($card['Manufacturer'], 'UTF-8', 'CP866'),
                'card' => mb_convert_encoding($card['Caption'], 'UTF-8', 'CP866'),
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

        foreach ($this->parseWmicListData($this->callExt->exec('wmic.exe', 'CPU GET /FORMAT:list')) as $proc) {
            $result['threads'] += (int)(isset($proc['ThreadCount']) ? $proc['ThreadCount'] : $proc['NumberOfLogicalProcessors']);
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
        return array(); // TODO
    }

    /**
     * getDistro.
     *
     * @return array the distro,version or false
     */
    public function getDistro()
    {
        return array();
    }

    /**
     * getCPUArchitecture.
     *
     * @return string the arch and bits
     */
    public function getCPUArchitecture()
    {
        $architecture = $this->parseWmicListData($this->callExt->exec('wmic.exe', 'CPU GET /FORMAT:list'))[0]['Architecture'];

        switch ($architecture) {
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
     * Fix error 'Method getModel not present' in Windows (XAMPP).
     *
     * @access public
     * @return string
     */
    public function getModel()
    {
        return $this->systemInfo['System Model'];
    }

    public function getCPUUsage()
    {
        return null;
    }

    public function getPhpVersion()
    {
        return PHP_VERSION;
    }
}
