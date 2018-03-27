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

class Mbmon implements Parser
{
    final private function __construct()
    {
    }

    final private function __clone()
    {
    }

    /**
     * Connect to host/port and get info
     *
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @return null|string
     */
    private function getData($host, $port, $timeout)
    {
        $sock = @\fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$sock) {
            return null;
        }

        $data = '';
        while ($mid = \fgets($sock)) {
            $data .= $mid;
        }
        \fclose($sock);

        return $data;
    }

    /**
     * Parse and return info from daemon socket
     *
     * @param string $data
     * @return array
     */
    private function parseSockData($data)
    {
        $return = [];

        $lines = \explode("\n", \trim($data));
        foreach ($lines as $line) {
            if (\preg_match('/(\w+)\s*:\s*([-+]?[\d\.]+)/i', $line, $match) === 1) {
                $return[] = [
                    'path' => 'N/A',
                    'name' => $match[1],
                    'temp' => $match[2],
                    'unit' => null, // todo
                ];
            }
        }

        return $return;
    }

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @return array|null
     */
    public static function work($host = 'localhost', $port = 411, $timeout = 1)
    {
        $obj = new self();
        $data = $obj->getData($host, $port, $timeout);
        if (null === $data) {
            return null;
        }

        return $obj->parseSockData($data);
    }
}
