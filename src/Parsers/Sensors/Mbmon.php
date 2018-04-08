<?php

namespace Linfo\Parsers\Sensors;

use Linfo\Parsers\Parser;

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
    private function getData(string $host, int $port, int $timeout) : ?string
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
    private function parseSockData(string $data) : array
    {
        $return = [];

        $lines = \explode("\n", \trim($data));
        foreach ($lines as $line) {
            if (\preg_match('/(\w+)\s*:\s*([-+]?[\d\.]+)/i', $line, $match) === 1) {
                $return[] = [
                    'path' => null,
                    'name' => $match[1],
                    'value' => $match[2],
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
    public static function work(string $host = 'localhost', int $port = 411, int $timeout = 1) : ?array
    {
        $obj = new self();
        $data = $obj->getData($host, $port, $timeout);
        if (null === $data) {
            return null;
        }

        return $obj->parseSockData($data);
    }
}
