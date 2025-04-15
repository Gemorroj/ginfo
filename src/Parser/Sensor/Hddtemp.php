<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\Parser\ParserInterface;

final readonly class Hddtemp implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{path: string|null, name:  string, value: float, unit: string}|null
     */
    public static function work(string $host = 'localhost', int $port = 7634, int $timeout = 1): ?array
    {
        $data = self::getData($host, $port, $timeout);
        if (null === $data) {
            return null;
        }

        return self::parseSockData($data);
    }

    /**
     * Connect to host/port and get info.
     */
    private static function getData(string $host, int $port, int $timeout): ?string
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
    private static function parseSockData(string $data): array
    {
        // Kill surrounding ||'s and split it by pipes
        $drives = \explode('||', \trim($data, '|'));

        $return = [];
        foreach ($drives as $drive) {
            [$path, $name, $temp, $unit] = \explode('|', \trim($drive));

            // Ignore garbled output from SSDs that hddtemp cant parse
            if (\str_contains($temp, 'UNK')) {
                continue;
            }

            // Ignore no longer existant devices?
            if (!\file_exists($path) && \is_readable('/dev')) {
                continue;
            }

            $return[] = [
                'path' => $path,
                'name' => $name,
                'value' => (float) $temp,
                'unit' => \mb_strtoupper($unit),
            ];
        }

        return $return;
    }
}
