<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\Parser\ParserInterface;

final readonly class Hddtemp implements ParserInterface
{
    /**
     * @return array{path: string|null, name: string, value: float, unit: string}[]|null
     */
    public function run(string $host = 'localhost', int $port = 7634, int $timeout = 1): ?array
    {
        $data = self::getData($host, $port, $timeout);
        if (null === $data) {
            return null;
        }

        $sensors = self::parseSockData($data);

        // Ignore devices that no longer exist
        if (\is_readable('/dev')) {
            $sensors = \array_values(\array_filter($sensors, static function (array $sensor): bool {
                return \file_exists($sensor['path']);
            }));
        }

        return $sensors;
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
     *
     * @return array{path: string, name: string, value: float, unit: string}[]
     */
    private static function parseSockData(string $data): array
    {
        // Kill surrounding ||'s and split it by pipes
        $drives = \explode('||', \trim($data, '|'));

        $return = [];
        foreach ($drives as $drive) {
            $parts = \explode('|', \trim($drive));
            if (4 !== \count($parts)) {
                continue;
            }
            [$path, $name, $temp, $unit] = $parts;

            // Ignore garbled output from SSDs that hddtemp cant parse
            if (\str_contains($temp, 'UNK')) {
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
