<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\Parser;

class Mbmon implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Connect to host/port and get info.
     */
    private function getData(string $host, int $port, int $timeout): ?string
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
     * Parse and return info from daemon socket.
     */
    private function parseSockData(string $data): array
    {
        $return = [];

        $lines = \explode("\n", \trim($data));
        foreach ($lines as $line) {
            if (1 === \preg_match('/(\w+)\s*:\s*([-+]?[\d\.]+)/i', $line, $match)) {
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

    public static function work(string $host = 'localhost', int $port = 411, int $timeout = 1): ?array
    {
        $obj = new self();
        $data = $obj->getData($host, $port, $timeout);
        if (null === $data) {
            return null;
        }

        return $obj->parseSockData($data);
    }
}
