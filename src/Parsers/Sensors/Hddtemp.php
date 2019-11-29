<?php

namespace Ginfo\Parsers\Sensors;

use Ginfo\Parsers\Parser;

class Hddtemp implements Parser
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
        // Kill surounding ||'s and split it by pipes
        $drives = \explode('||', \trim($data, '|'));

        $return = [];
        foreach ($drives as $drive) {
            [$path, $name, $temp, $unit] = \explode('|', \trim($drive));

            // Ignore garbled output from SSDs that hddtemp cant parse
            if (false !== \mb_strpos($temp, 'UNK')) {
                continue;
            }

            // Ignore no longer existant devices?
            if (!\file_exists($path) && \is_readable('/dev')) {
                continue;
            }

            $return[] = [
                'path' => $path,
                'name' => $name,
                'value' => $temp,
                'unit' => \mb_strtoupper($unit),
            ];
        }

        return $return;
    }

    public static function work(string $host = 'localhost', int $port = 7634, int $timeout = 1): ?array
    {
        $obj = new self();
        $data = $obj->getData($host, $port, $timeout);
        if (null === $data) {
            return null;
        }

        return $obj->parseSockData($data);
    }
}
