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

namespace Linfo\Parsers;

use Linfo\Common;
use Linfo\Meta\Settings;

/**
 * Deal with hddtemp
 */
class Hddtemp
{
    // Store these
    protected $mode;
    protected $host;
    protected $port;

    // Default socket connect timeout
    const TIMEOUT = 3;

    // Localize mode
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Localize host and port for connecting to HDDTemp daemon
     * @param string $host
     * @param int $port
     */
    public function setAddress($host, $port = 7634)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Connect to host/port and get info
     *
     * @return string
     * @throws \Exception
     */
    private function getSock()
    {
        // Try connecting
        if (!($sock = @fsockopen($this->host, $this->port, $errno, $errstr, self::TIMEOUT))) {
            throw new \Exception('Error connecting');
        }

        // Try getting stuff
        $buffer = '';
        while ($mid = @fgets($sock)) {
            $buffer .= $mid;
        }

        // Quit
        @fclose($sock);

        // Output:
        return $buffer;
    }

    /**
     * Parse and return info from daemon socket
     *
     * @param string $data
     * @return array
     */
    private function parseSockData($data)
    {

        // Kill surounding ||'s and split it by pipes
        $drives = explode('||', trim($data, '|'));

        // Return our stuff here
        $return = array();

        // Go through each
        foreach ($drives as $drive) {

            // Extract stuff from it
            list($path, $name, $temp, $unit) = explode('|', trim($drive));

            // Ignore garbled output from SSDs that hddtemp cant parse
            if (\mb_strpos($temp, 'UNK') !== false) {
                continue;
            }

            // Ignore /dev/sg?
            if (!empty(Settings::getInstance()->getSettings()['hide']['sg']) && \mb_substr($path, 0, 7) == '/dev/sg') {
                continue;
            }

            // Ignore no longer existant devices?
            if (!file_exists($path) && is_readable('/dev')) {
                continue;
            }

            // Save it
            $return[] = array(
                'path' => $path,
                'name' => $name,
                'temp' => $temp,
                'unit' => \mb_strtoupper($unit),
            );
        }

        // Give off results
        return $return;
    }

    /**
     * For parsing the syslog looking for hddtemp entries
     * POTENTIALLY BUGGY -- only tested on debian/ubuntu flavored syslogs
     * Also slow as balls as it parses the entire syslog instead of
     * using something like tail
     * @return array
     */
    private function parseSysLogData()
    {
        $file = '/var/log/syslog';
        if (!is_file($file) || !is_readable($file)) {
            return array();
        }
        $devices = array();
        foreach (Common::getLines($file) as $line) {
            if (preg_match('/\w+\s*\d+ \d{2}:\d{2}:\d{2} \w+ hddtemp\[\d+\]: (.+): (.+): (\d+) ([CF])/i', trim($line), $match) == 1) {
                // Replace current record of dev with updated temp
                $devices[$match[1]] = array($match[2], $match[3], $match[4]);
            }
        }
        $return = array();
        foreach ($devices as $dev => $stat) {
            $return[] = array(
                'path' => $dev,
                'name' => $stat[0],
                'temp' => $stat[1],
                'unit' => \mb_strtoupper($stat[2]),
            );
        }

        return $return;
    }

    /**
     * Wrapper function around the private ones here which do the
     * actual work, and returns temps
     * Use supplied mode, and optionally host/port, to get temps and return them
     * @throws \Exception
     * @return array
     */
    public function work()
    {
        // Deal with differences in mode
        switch ($this->mode) {

            // Connect to daemon mode
            case 'daemon':
                return $this->parseSockData($this->getSock());
                break;

            // Syslog every n seconds
            case 'syslog':
                return $this->parseSysLogData();
                break;

            // Some other mode
            default:
                throw new \Exception('Not supported mode');
                break;
        }
    }
}
