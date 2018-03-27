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

use Linfo\Meta\Errors;
use Linfo\Common;
use Linfo\Exceptions\FatalException;
use Linfo\Meta\Settings;
use Linfo\Parsers\Hwpci;
use Linfo\Parsers\Sensord;
use Linfo\Parsers\Hddtemp;
use Linfo\Parsers\Mbmon;
use Symfony\Component\Process\Process;


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


    public function getMemory()
    {
        $free = (new Process('free -bw'))->mustRun()->getOutput();

        $arr = \explode("\n", $free);
        unset($arr[0]); // remove header

        $memStr = \trim(\ltrim($arr[1], 'Mem:'));
        $swapStr = \trim(\ltrim($arr[2], 'Swap:'));

        list($memTotal, $memUsed, $memFree, $memShared, $memBuffers, $memCached, $memAvailable) = \preg_split('/\s+/', $memStr);
        list($swapTotal, $swapUsed, $swapFree) = \preg_split('/\s+/', $swapStr);

        return [
            'memoryTotal' => $memTotal,
            'memoryUsed' => $memUsed,
            'memoryFree' => $memFree,
            'memoryShared' => $memShared,
            'memoryBuffers' => $memBuffers,
            'memoryCached' => $memCached,

            'swapTotal' => $swapTotal,
            'swapUsed' => $swapUsed,
            'swapFree' => $swapFree,
        ];
    }

    /**
     * @param string $block
     * @return array
     */
    private function parseProcBlock($block)
    {
        $tmp = [];
        foreach (\explode("\n", $block) as $line) {
            if (false !== \strpos($line, ':')) {
                @list($key, $value) = \explode(':', $line, 2);
                $tmp[\trim($key)] = \trim($value);
            }
        }
        return $tmp;
    }

    public function getCpu()
    {
        $cpuInfo = Common::getContents('/proc/cpuinfo');
        $cpuData = [];
        foreach (\explode("\n\n", $cpuInfo) as $block) {
            $cpuData[] = $this->parseProcBlock($block);
        }


        $detectPhysical = function (array $cpuData) {
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
        $detectVirtual = function (array $cpuData) {
            echo \count($cpuData);
        };
        $detectCores = function (array $cpuData) {
            $out = [];
            foreach ($cpuData as $block) {
                $out[$block['physical id']] = $block['cpu cores'];
            }

            return \array_sum($out);
        };

        $detectInfo = function (array $cpuData) {
            $out = [];
            foreach ($cpuData as $block) {
                $out[$block['physical id']]['model'] = $block['model name'];
                $out[$block['physical id']]['speed'] = $block['cpu MHz'];
                $out[$block['physical id']]['cache'] = $block['cache size']; // L2 cache
                $out[$block['physical id']]['flags'] = $block['flags'];
            }

            return $out;
        };

        return [
            'physical' => $detectPhysical($cpuData),
            'virtual' => $detectVirtual($cpuData),
            'cores' => $detectCores($cpuData),
            'processor' => $detectInfo($cpuData),
        ];
    }



    public function getUptime()
    {
        $uptime = Common::getContents('/proc/uptime');

        if (null === $uptime) {
            return null;
        }

        return \round(\explode(' ', $uptime, 2)[0]);
    }

    /**
     * getHD.
     *
     * @return array the hard drive info
     */
    public function getHD()
    {
        // Get partitions
        $partitions = array();
        $partitions_contents = Common::getContents('/proc/partitions');
        if (@preg_match_all('/(\d+)\s+([a-z]{3})(\d+)$/m', $partitions_contents, $partitions_match, PREG_SET_ORDER) > 0) {
            // Go through each match
            foreach ($partitions_match as $partition) {
                $partitions[$partition[2]][] = array(
                    'size' => $partition[1] * 1024,
                    'number' => $partition[3],
                );
            }
        }

        // Store drives here
        $drives = array();

        // Get actual drives
        foreach ((array)@glob('/sys/block/*/device/model', GLOB_NOSORT) as $path) {

            // Parts of the path
            $parts = explode('/', $path);

            // Attempt getting read/write stats
            if (preg_match('/^(\d+)\s+\d+\s+\d+\s+\d+\s+(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+$/', Common::getContents(dirname(dirname($path)) . '/stat'), $statMatches) !== 1) {
                // Didn't get it
                $reads = false;
                $writes = false;
            } else {
                // Got it, save it
                list(, $reads, $writes) = $statMatches;
            }

            // Append this drive on
            $drives[] = array(
                'name' => Common::getContents($path, 'Unknown') . (Common::getContents(dirname(dirname($path)) . '/queue/rotational') == 0 ? ' (SSD)' : ''),
                'vendor' => Common::getContents(dirname($path) . '/vendor', 'Unknown'),
                'device' => '/dev/' . $parts[3],
                'reads' => $reads,
                'writes' => $writes,
                'size' => Common::getContents(dirname(dirname($path)) . '/size', 0) * 512,
                'partitions' => array_key_exists($parts[3], $partitions) && is_array($partitions[$parts[3]]) ? $partitions[$parts[3]] : false,
            );
        }

        // Return drives
        return $drives;
    }

    /**
     * getTemps.
     *
     * @return array the temps
     */
    public function getTemps()
    {
        // Hold them here
        $return = array();

        // hddtemp?
        if (array_key_exists('hddtemp', (array)Settings::getInstance()->getSettings()['temps']) && !empty(Settings::getInstance()->getSettings()['temps']['hddtemp']) && isset(Settings::getInstance()->getSettings()['hddtemp'])) {
            try {
                // Initiate class
                $hddtemp = new Hddtemp();

                // Set mode, as in either daemon or syslog
                $hddtemp->setMode(Settings::getInstance()->getSettings()['hddtemp']['mode']);

                // If we're daemon, save host and port
                if (Settings::getInstance()->getSettings()['hddtemp']['mode'] == 'daemon') {
                    $hddtemp->setAddress(
                        Settings::getInstance()->getSettings()['hddtemp']['address']['host'],
                        Settings::getInstance()->getSettings()['hddtemp']['address']['port']);
                }

                // Result after working it
                $hddtemp_res = $hddtemp->work();

                // If it's an array, it worked
                if (is_array($hddtemp_res)) {
                    // Save result
                    $return = array_merge($return, $hddtemp_res);
                }
            } // There was an issue
            catch (\Exception $e) {
                Errors::add('hddtemp parser', $e->getMessage());
            }
        }

        // mbmon?
        if (array_key_exists('mbmon', (array)Settings::getInstance()->getSettings()['temps']) && !empty(Settings::getInstance()->getSettings()['temps']['mbmon']) && isset(Settings::getInstance()->getSettings()['mbmon'])) {
            try {
                // Initiate class
                $mbmon = new Mbmon();

                // Set host and port
                $mbmon->setAddress(
                    Settings::getInstance()->getSettings()['mbmon']['address']['host'],
                    Settings::getInstance()->getSettings()['mbmon']['address']['port']);

                // Get result after working it
                $mbmon_res = $mbmon->work();

                // If it's an array, it worked
                if (is_array($mbmon_res)) {
                    // Save result
                    $return = array_merge($return, $mbmon_res);
                }
            } catch (\Exception $e) {
                Errors::add('mbmon parser', $e->getMessage());
            }
        }

        // sensord? (part of lm-sensors)
        if (array_key_exists('sensord', (array)Settings::getInstance()->getSettings()['temps']) && !empty(Settings::getInstance()->getSettings()['temps']['sensord'])) {
            try {
                // Iniatate class
                $sensord = new Sensord();

                // Work it
                $sensord_res = $sensord->work();

                // If it's an array, it worked
                if (is_array($sensord_res)) {
                    // Save result
                    $return = array_merge($return, $sensord_res);
                }
            } catch (\Exception $e) {
                Errors::add('sensord parser', $e->getMessage());
            }
        }

        // hwmon? (probably the fastest of what's here)
        // too simple to be in its own class
        if (array_key_exists('hwmon', (array)Settings::getInstance()->getSettings()['temps']) && !empty(Settings::getInstance()->getSettings()['temps']['hwmon'])) {

            // Store them here
            $hwmon_vals = array();

            // Wacky location
            foreach ((array)@glob('/sys/class/hwmon/hwmon*/{,device/}*_input', GLOB_NOSORT | GLOB_BRACE) as $path) {
                $initpath = rtrim($path, 'input');
                $value = Common::getContents($path);
                $base = basename($path);
                $labelpath = $initpath . 'label';
                $showemptyfans = isset(Settings::getInstance()->getSettings()['temps_show0rpmfans']) ? Settings::getInstance()->getSettings()['temps_show0rpmfans'] : false;
                $drivername = @basename(@readlink(dirname($path) . '/driver')) ?: false;

                // Temperatures
                if (is_file($labelpath) && strpos($base, 'temp') === 0) {
                    $label = Common::getContents($labelpath);
                    $value /= $value > 10000 ? 1000 : 1;
                    $unit = 'C'; // I don't think this is ever going to be in F
                } // Fan RPMs
                elseif (preg_match('/^fan(\d+)_/', $base, $m)) {
                    $label = 'fan' . $m[1];
                    $unit = 'RPM';

                    if ($value == 0 && !$showemptyfans) {
                        continue;
                    }
                } // Volts
                elseif (preg_match('/^in(\d+)_/', $base, $m)) {
                    $unit = 'V';
                    $value /= 1000;
                    $label = Common::getContents($labelpath) ?: 'in' . $m[1];
                } else {
                    continue;
                }

                // Append values
                $hwmon_vals[] = array(
                    'path' => '',
                    'name' => $label . ($drivername ? ' (' . $drivername . ')' : ''),
                    'temp' => $value,
                    'unit' => $unit,
                );
            }

            // Save any if we have any
            if (count($hwmon_vals) > 0) {
                $return = array_merge($return, $hwmon_vals);
            }
        }

        // thermal_zone? 
        if (array_key_exists('thermal_zone', (array)Settings::getInstance()->getSettings()['temps']) && !empty(Settings::getInstance()->getSettings()['temps']['thermal_zone'])) {

            // Store them here
            $thermal_zone_vals = array();

            // Wacky location
            foreach ((array)@glob('/sys/class/thermal/thermal_zone*', GLOB_NOSORT | GLOB_BRACE) as $path) {
                $labelpath = $path . DIRECTORY_SEPARATOR . 'type';
                $valuepath = $path . DIRECTORY_SEPARATOR . 'temp';

                if (!is_file($labelpath) || !is_file($valuepath)) {
                    continue;
                }

                // Temperatures
                $label = Common::getContents($labelpath);
                $value = Common::getIntFromFile($valuepath);
                $value /= $value > 10000 ? 1000 : 1;

                // Append values
                $thermal_zone_vals[] = array(
                    'path' => $path,
                    'name' => $label,
                    'temp' => $value,
                    'unit' => 'C', // I don't think this is ever going to be in F
                );
            }

            // Save any if we have any
            if (count($thermal_zone_vals) > 0) {
                $return = array_merge($return, $thermal_zone_vals);
            }
        }

        // Laptop backlight percentage
        foreach ((array)@glob('/sys/{devices/virtual,class}/backlight/*/max_brightness', GLOB_NOSORT | GLOB_BRACE) as $bl) {
            $dir = dirname($bl);
            if (!is_file($dir . '/actual_brightness')) {
                continue;
            }
            $max = Common::getIntFromFile($bl);
            $cur = Common::getIntFromFile($dir . '/actual_brightness');
            if ($max < 0 || $cur < 0) {
                continue;
            }
            $return[] = array(
                'name' => 'Backlight brightness',
                'temp' => round($cur / $max, 2) * 100,
                'unit' => '%',
                'path' => 'N/A',
                'bar' => true,
            );
        }

        // Done
        return $return;
    }

    /**
     * getMounts.
     *
     * @return array the mounted the file systems
     */
    public function getMounts()
    {
        // File
        $contents = Common::getContents('/proc/mounts', false);

        // Can't?
        if ($contents == false) {
            Errors::add('Linfo Core', '/proc/mounts does not exist');
        }

        // Parse
        if (@preg_match_all('/^(\S+) (\S+) (\S+) (.+) \d \d$/m', $contents, $match, PREG_SET_ORDER) === false) {
            Errors::add('Linfo Core', 'Error parsing /proc/mounts');
        }

        // Return these
        $mounts = array();

        // Populate
        foreach ($match as $mount) {

            // Should we not show this?
            if (in_array($mount[1], Settings::getInstance()->getSettings()['hide']['storage_devices']) || in_array($mount[3], Settings::getInstance()->getSettings()['hide']['filesystems'])) {
                continue;
            }

            // Should we not show this? (regex)
            if (isset(Settings::getInstance()->getSettings()['hide']['mountpoints_regex']) && is_array(Settings::getInstance()->getSettings()['hide']['mountpoints_regex'])) {
                foreach (Settings::getInstance()->getSettings()['hide']['mountpoints_regex'] as $regex) {
                    if (@preg_match($regex, $mount[2])) {
                        continue 2;
                    }
                }
            }

            // Spaces and other things in the mount path are escaped C style. Fix that.
            $mount[2] = stripcslashes($mount[2]);

            // Get these
            $size = @disk_total_space($mount[2]);
            $free = @disk_free_space($mount[2]);
            $used = $size != false && $free != false ? $size - $free : false;

            // If it's a symlink, find out where it really goes.
            // (using realpath instead of readlink because the former gives absolute paths)
            if (isset(Settings::getInstance()->getSettings()['hide']['dont_resolve_mountpoint_symlinks']) && Settings::getInstance()->getSettings()['hide']['dont_resolve_mountpoint_symlinks']) {
                $symlink = false;
            } else {
                $symlink = is_link($mount[1]) ? realpath($mount[1]) : false;
            }

            // Optionally get mount options
            if (Settings::getInstance()->getSettings()['show']['mounts_options'] && !in_array($mount[3], (array)Settings::getInstance()->getSettings()['hide']['fs_mount_options'])) {
                $mount_options = explode(',', $mount[4]);
            } else {
                $mount_options = array();
            }

            // Might be good, go for it
            $mounts[] = array(
                'device' => $symlink != false ? $symlink : $mount[1],
                'mount' => $mount[2],
                'type' => $mount[3],
                'size' => $size,
                'used' => $used,
                'free' => $free,
                'free_percent' => ((bool)$free != false && (bool)$size != false ? round($free / $size, 2) * 100 : false),
                'used_percent' => ((bool)$used != false && (bool)$size != false ? round($used / $size, 2) * 100 : false),
                'options' => $mount_options,
            );
        }

        // Return
        return $mounts;
    }


    /**
     * usbutils wrapper
     * @return array|null
     */
    public function getUsb()
    {
        $usbIds = Common::locateActualPath([
            '/usr/share/misc/usb.ids',    // debian/ubuntu
            '/usr/share/usb.ids',        // opensuse
            '/usr/share/hwdata/usb.ids',    // centos. maybe also redhat/fedora
        ]);

        if (!$usbIds) {
            return null;
        }

        return Hwpci::workUsb($usbIds);
    }

    /**
     * pciutils wrapper
     * @return array|null
     */
    public function getPci()
    {
        $pciIds = Common::locateActualPath([
            '/usr/share/misc/pci.ids',    // debian/ubuntu
            '/usr/share/pci.ids',        // opensuse
            '/usr/share/hwdata/pci.ids',    // centos. maybe also redhat/fedora
        ]);

        if (!$pciIds) {
            return null;
        }

        return Hwpci::workPci($pciIds);
    }

    /**
     * getRAID.
     *
     * @return array of raid arrays
     */
    public function getRAID()
    {
        // Store it here
        $raidinfo = array();

        // mdadm?
        if (array_key_exists('mdadm', (array)Settings::getInstance()->getSettings()['raid']) && !empty(Settings::getInstance()->getSettings()['raid']['mdadm'])) {

            // Try getting contents
            $mdadm_contents = Common::getContents('/proc/mdstat', false);

            // No?
            if ($mdadm_contents === false) {
                Errors::add('Linux softraid mdstat parser', '/proc/mdstat does not exist.');
            }

            // Parse
            @preg_match_all('/(\S+)\s*:\s*(\w+)\s*raid(\d+)\s*([\w+\[\d+\] (\(\w\))?]+)\n\s+(\d+) blocks[^[]+\[(\d\/\d)\] \[([U\_]+)\]/mi', (string)$mdadm_contents, $match, PREG_SET_ORDER);

            // Store them here
            $mdadm_arrays = array();

            // Deal with entries
            foreach ((array)$match as $array) {

                // Temporarily store drives here
                $drives = array();

                // Parse drives
                foreach (explode(' ', $array[4]) as $drive) {

                    // Parse?
                    if (preg_match('/([\w\d]+)\[\d+\](\(\w\))?/', $drive, $match_drive) == 1) {

                        // Determine a status other than normal, like if it failed or is a spare
                        if (array_key_exists(2, $match_drive)) {
                            switch ($match_drive[2]) {
                                case '(S)':
                                    $drive_state = 'spare';
                                    break;
                                case '(F)':
                                    $drive_state = 'failed';
                                    break;
                                case null:
                                    $drive_state = 'normal';
                                    break;

                                // I'm not sure if there are status codes other than the above
                                default:
                                    $drive_state = 'unknown';
                                    break;
                            }
                        } else {
                            $drive_state = 'normal';
                        }

                        // Append this drive to the temp drives array
                        $drives[] = array(
                            'drive' => '/dev/' . $match_drive[1],
                            'state' => $drive_state,
                        );
                    }
                }

                // Add record of this array to arrays list
                $mdadm_arrays[] = array(
                    'device' => '/dev/' . $array[1],
                    'status' => $array[2],
                    'level' => $array[3],
                    'drives' => $drives,
                    'size' => Common::byteConvert($array[5] * 1024),
                    'count' => $array[6],
                    'chart' => $array[7],
                );
            }

            // Append MD arrays to main raidinfo if it's good
            if (is_array($mdadm_arrays) && count($mdadm_arrays) > 0) {
                $raidinfo = array_merge($raidinfo, $mdadm_arrays);
            }
        }

        // Return info
        return $raidinfo;
    }


    public function getLoad()
    {
        $contents = Common::getContents('/proc/loadavg');
        if (null === $contents) {
            return [];
        }

        $parts = \array_slice(\explode(' ', $contents), 0, 3);
        if (!$parts) {
            return [];
        }

        return \array_combine(['now', '5min', '15min'], $parts);
    }

    /**
     * getNet.
     *
     * @return array of network devices
     */
    public function getNet()
    {
        // Hold our return values
        $return = array();

        // Get values for each device
        foreach ((array)@glob('/sys/class/net/*', GLOB_NOSORT) as $path) {
            $nic = basename($path);

            // States
            $operstate_contents = Common::getContents($path . '/operstate');
            switch ($operstate_contents) {
                case 'down':
                case 'up':
                case 'unknown':
                    $state = $operstate_contents;
                    break;

                default:
                    $state = 'unknown';
                    break;
            }

            if ($state = 'unknown' && file_exists($path . '/carrier')) {
                $carrier = Common::getContents($path . '/carrier', false);
                if (!empty($carrier)) {
                    $state = 'up';
                } else {
                    $state = 'down';
                }
            }

            // Try the weird ways of getting type (https://stackoverflow.com/a/16060638)
            $type = false;
            $typeCode = Common::getIntFromFile($path . '/type');

            if ($typeCode == 772) {
                $type = 'Loopback';
            } elseif ($typeCode == 65534) {
                $type = 'Tunnel';
            } elseif ($typeCode == 776) {
                $type = 'IPv6 in IPv4';
            }

            if (!$type) {
                $type_contents = strtoupper(Common::getContents($path . '/device/modalias'));
                list($type_match) = explode(':', $type_contents, 2);

                if (in_array($type_match, array('PCI', 'USB'))) {
                    $type = 'Ethernet (' . $type_match . ')';

                    // Driver maybe?
                    if (($uevent_contents = @parse_ini_file($path . '/device/uevent')) && isset($uevent_contents['DRIVER'])) {
                        $type .= ' (' . $uevent_contents['DRIVER'] . ')';
                    }
                } elseif ($type_match == 'VIRTIO') {
                    $type = 'VirtIO';
                } elseif ($type_contents == 'XEN:VIF') {
                    $type = 'Xen (VIF)';
                } elseif ($type_contents == 'XEN-BACKEND:VIF') {
                    $type = 'Xen Backend (VIF)';
                } elseif (is_dir($path . '/bridge')) {
                    $type = 'Bridge';
                } elseif (is_dir($path . '/bonding')) {
                    $type = 'Bond';
                }

                // TODO find some way of finding out what provides the virt-specific kvm vnet devices
            }

            $speed = Common::getIntFromFile($path . '/speed');

            // Save and get info for each
            $return[$nic] = array(

                // Stats are stored in simple files just containing the number
                'recieved' => array(
                    'bytes' => Common::getIntFromFile($path . '/statistics/rx_bytes'),
                    'errors' => Common::getIntFromFile($path . '/statistics/rx_errors'),
                    'packets' => Common::getIntFromFile($path . '/statistics/rx_packets'),
                ),
                'sent' => array(
                    'bytes' => Common::getIntFromFile($path . '/statistics/tx_bytes'),
                    'errors' => Common::getIntFromFile($path . '/statistics/tx_errors'),
                    'packets' => Common::getIntFromFile($path . '/statistics/tx_packets'),
                ),

                // These were determined above
                'state' => $state,
                'type' => $type ?: 'N/A',
                'port_speed' => $speed > 0 ? $speed : false,
            );
        }

        // Return array of info
        return $return;
    }

    /**
     * getBattery.
     *
     * @return array of battery status
     */
    public function getBattery()
    {
        // Return values
        $return = array();

        // Here they should be
        $bats = (array)@glob('/sys/class/power_supply/BAT*', GLOB_NOSORT);

        // Get vals for each battery
        foreach ($bats as $b) {
            foreach (array($b . '/manufacturer', $b . '/status') as $f) {
                if (!is_file($f)) {
                    continue 2;
                }
            }

            // Get these from the simple text files
            switch (true) {
                case is_file($b . '/energy_full'):
                    $charge_full = Common::getIntFromFile($b . '/energy_full');
                    $charge_now = Common::getIntFromFile($b . '/energy_now');
                    break;
                case is_file($b . '/charge_full'):
                    $charge_full = Common::getIntFromFile($b . '/charge_full');
                    $charge_now = Common::getIntFromFile($b . '/charge_now');
                    break;
                default:
                    continue;
                    break;
            }

            // Alleged percentage
            $percentage = $charge_now != 0 && $charge_full != 0 ? (round($charge_now / $charge_full, 4) * 100) : '?';

            // Save result set
            $return[] = array(
                'charge_full' => $charge_full,
                'charge_now' => $charge_now,
                'percentage' => (is_numeric($percentage) && $percentage > 100 ? 100 : $percentage),
                'device' => Common::getContents($b . '/manufacturer') . ' ' . Common::getContents($b . '/model_name', 'Unknown'),
                'state' => Common::getContents($b . '/status', 'Unknown'),
            );
        }

        // Give it
        return $return;
    }

    /**
     * getWifi.
     *
     * @return array of wifi devices
     */
    public function getWifi()
    {
        // Return these
        $return = array();

        // In here
        $contents = Common::getContents('/proc/net/wireless');

        // Oi
        if ($contents == false) {
            Errors::add('Linux WiFi info parser', '/proc/net/wireless does not exist');

            return $return;
        }

        // Parse
        @preg_match_all('/^ (\S+)\:\s*(\d+)\s*(\S+)\s*(\S+)\s*(\S+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*$/m', $contents, $match, PREG_SET_ORDER);

        // Match
        foreach ($match as $wlan) {
            $return[] = array(
                'device' => $wlan[1],
                'status' => $wlan[2],
                'quality_link' => $wlan[3],
                'quality_level' => $wlan[4],
                'quality_noise' => $wlan[5],
                'dis_nwid' => $wlan[6],
                'dis_crypt' => $wlan[7],
                'dis_frag' => $wlan[8],
                'dis_retry' => $wlan[9],
                'dis_misc' => $wlan[10],
                'mis_beac' => $wlan[11],
            );
        }

        // Done
        return $return;
    }


    public function getSoundCards()
    {
        $contents = Common::getContents('/proc/asound/cards');
        if (null === $contents) {
            return [];
        }

        if (\preg_match_all('/^\s*(\d+)\s\[[\s\w]+\]:\s(.+)$/m', $contents, $matches, \PREG_SET_ORDER) === 0) {
            return [];
        }

        $cards = [];
        foreach ($matches as $card) {
            $cards[] = array(
                'number' => $card[1],
                'card' => $card[2],
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
        // We'll return this after stuffing it with useful info
        $result = array(
            'exists' => true,
            'totals' => array(
                'running' => 0,
                'zombie' => 0,
                'sleeping' => 0,
                'stopped' => 0,
            ),
            'proc_total' => 0,
            'threads' => 0,
        );

        // Get all the paths to each process' status file
        $processes = (array)@glob('/proc/*/status', GLOB_NOSORT);

        // Total
        $result['proc_total'] = count($processes);

        // Go through each
        foreach ($processes as $process) {

            // Don't waste time if we can't use it
            if (!is_readable($process)) {
                continue;
            }

            // Get that file's contents
            $status_contents = Common::getContents($process);

            // Try getting state
            @preg_match('/^State:\s+(\w)/m', $status_contents, $state_match);

            // Well? Determine state
            switch ($state_match[1]) {
                case 'D': // disk sleep? wtf?
                case 'S':
                    $result['totals']['sleeping']++;
                    break;
                case 'Z':
                    $result['totals']['zombie']++;
                    break;
                case 'R':
                    $result['totals']['running']++;
                    break;
                case 'T':
                    $result['totals']['stopped']++;
                    break;
            }

            // Try getting number of threads
            @preg_match('/^Threads:\s+(\d+)/m', $status_contents, $threads_match);

            // Well?
            if ($threads_match) {
                list(, $threads) = $threads_match;
            }

            // Append it on if it's good
            if (is_numeric($threads)) {
                $result['threads'] = $result['threads'] + $threads;
            }
        }

        // Give off result
        return $result;
    }

    /**
     * getServices.
     *
     * @return array the services
     */
    public function getServices()
    {
        // We allowed?
        if (empty(Settings::getInstance()->getSettings()['show']['services']) || !is_array(Settings::getInstance()->getSettings()['services']) || count(Settings::getInstance()->getSettings()['services']) == 0) {
            return array();
        }

        // Temporarily keep statuses here
        $statuses = array();

        Settings::getInstance()->getSettings()['services']['executables'] = (array)Settings::getInstance()->getSettings()['services']['executables'];
        Settings::getInstance()->getSettings()['services']['pidFiles'] = (array)Settings::getInstance()->getSettings()['services']['pidFiles'];
        Settings::getInstance()->getSettings()['services']['systemdServices'] = (array)Settings::getInstance()->getSettings()['services']['systemdServices'];

        // Convert paths of executables to PID files
        $pids = array();
        $do_process_search = false;
        if (count(Settings::getInstance()->getSettings()['services']['executables']) > 0) {
            $potential_paths = @glob('/proc/*/cmdline');
            if (is_array($potential_paths)) {
                $num_paths = count($potential_paths);
                $do_process_search = true;
            }
        }

        // Should we go ahead and do the PID search based on executables?
        if ($do_process_search) {
            // Precache all process cmdlines
            for ($i = 0; $i < $num_paths; ++$i) {
                $cmdline_cache[$i] = explode("\x00", Common::getContents($potential_paths[$i]));
            }

            // Go through the list of executables to search for
            foreach (Settings::getInstance()->getSettings()['services']['executables'] as $service => $exec) {
                // Go through pid file list. for loops are faster than foreach
                for ($i = 0; $i < $num_paths; ++$i) {
                    $cmdline = $cmdline_cache[$i];
                    $match = false;
                    if (is_array($exec)) {
                        $match = true;
                        foreach ($exec as $argn => $argv) {
                            if (isset($cmdline[$argn]) && $cmdline[$argn] != $argv) {
                                $match = false;
                            }
                        }
                    } elseif ($cmdline[0] == $exec) {
                        $match = true;
                    }
                    // If this one matches, stop here and save it
                    if ($match) {
                        // Get pid out of path to cmdline file
                        $pids[$service] = substr($potential_paths[$i], 6 /*strlen('/proc/')*/, strpos($potential_paths[$i], '/', 7) - 6);
                        break;
                    }
                }
            }
        }

        // PID files
        foreach (Settings::getInstance()->getSettings()['services']['pidFiles'] as $service => $file) {
            $pid = Common::getContents($file, false);
            if ($pid != false && is_numeric($pid)) {
                $pids[$service] = $pid;
            }
        }

        // systemd services
        foreach (Settings::getInstance()->getSettings()['services']['systemdServices'] as $service => $systemdService) {
            $process = new Process('systemctl show -p MainPID ' . $systemdService);
            $process->mustRun();
            $command = $process->getOutput();

            $command = trim($command);
            $pid = str_replace('MainPID=', '', $command);
            if ($pid != '' && is_numeric($pid)) {
                $pids[$service] = $pid;
            }
        }


        // Deal with PIDs
        foreach ($pids as $service => $pid) {
            $path = '/proc/' . $pid . '/status';
            $status_contents = Common::getContents($path, false);
            if ($status_contents == false) {
                $statuses[$service] = array('state' => 'Down', 'threads' => 'N/A', 'pid' => $pid);
                continue;
            }

            // Attempt getting info out of it
            if (!preg_match_all('/^(\w+):\s+(\w+)/m', $status_contents, $status_matches, PREG_SET_ORDER)) {
                continue;
            }

            // Initially set these as pointless
            $state = false;
            $threads = false;
            $mem = false;

            // Go through
            for ($i = 0, $num = count($status_matches); $i < $num; ++$i) {

                // What have we here?
                switch ($status_matches[$i][1]) {

                    // State section
                    case 'State':
                        switch ($status_matches[$i][2]) {
                            case 'D': // disk sleep? wtf?
                            case 'S':
                                $state = 'Up (Sleeping)';
                                break;
                            case 'Z':
                                $state = 'Zombie';
                                break;
                            // running
                            case 'R':
                                $state = 'Up (Running)';
                                break;
                            // stopped
                            case 'T':
                                $state = 'Up (Stopped)';
                                break;
                            default:
                                continue;
                                break;
                        }
                        break;

                    // Mem usage
                    case 'VmRSS':
                        if (is_numeric($status_matches[$i][2])) {
                            $mem = $status_matches[$i][2] * 1024;
                        } // Measured in kilobytes; we want bytes
                        break;

                    // Thread count
                    case 'Threads':
                        if (is_numeric($status_matches[$i][2])) {
                            $threads = $status_matches[$i][2];
                        }

                        // Thread count should be last. Stop here to possibly save time assuming we have the other values
                        if ($state !== false && $mem !== false && $threads !== false) {
                            break;
                        }
                        break;
                }
            }

            // Save info
            $statuses[$service] = array(
                'state' => $state ? $state : '?',
                'threads' => $threads,
                'pid' => $pid,
                'memory_usage' => $mem,
            );
        }

        return $statuses;
    }


    public function getOsName()
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

    public function getLoggedUsers()
    {
        // Snag command line of every process in system
        $procs = \glob('/proc/*/cmdline', \GLOB_NOSORT);

        $users = [];
        foreach ($procs as $proc) {
            // Does the process match a popular shell, such as bash, csh, etc?
            if (\preg_match('/(?:bash|csh|zsh|ksh)$/', Common::getContents($proc))) {

                // Who owns it, anyway? 
                $owner = \fileowner(\dirname($proc));
                if (!\is_numeric($owner)) {
                    continue;
                }
                if (!\in_array($owner, $users)) {
                    $users[] = $owner;
                }
            }
        }

        if (\function_exists('posix_getpwuid')) {
            \array_walk($users, function (&$item) {
                $item = \posix_getpwuid($item)['name'];
            });
        }

        return $users;
    }


    public function getVirtualization()
    {
        if (\is_file('/proc/vz/veinfo')) {
            return 'OpenVZ';
        }

        if (Common::getContents('/sys/devices/virtual/dmi/id/bios_vendor') === 'Veertu') {
            return 'Veertu';
        }

        if (\strpos(Common::getContents('/proc/mounts', ''), 'lxcfs /proc/') !== false) {
            return 'LXC';
        }

        if (\is_file('/.dockerenv') || \is_file('/.dockerinit') || \strpos(Common::getContents('/proc/1/cgroup', ''), 'docker') !== false) {
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
        foreach (@\glob('/sys/bus/pci/drivers/*') as $name) {
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
    public function getModel()
    {
        $info = [];
        $vendor = Common::getContents('/sys/devices/virtual/dmi/id/board_vendor');
        $name = Common::getContents('/sys/devices/virtual/dmi/id/board_name');
        $product = Common::getContents('/sys/devices/virtual/dmi/id/product_name');

        if (!$name) {
            return null;
        }

        // Don't add vendor to the mix if the name starts with it
        if ($vendor && \strpos($name, $vendor) !== 0) {
            $info[] = $vendor;
        }

        $info[] = $name;

        $infostr = \implode(' ', $info);

        // product name is usually bullshit, but *occasionally* it's a useful name of the computer, such as
        // dell latitude e6500 or hp z260
        if ($product && \strpos($name, $product) === false && \strpos($product, 'Filled') === false) {
            return $product . ' (' . $infostr . ')';
        } else {
            return $infostr;
        }
    }
}
