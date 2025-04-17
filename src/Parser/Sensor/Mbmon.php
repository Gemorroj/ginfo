<?php

namespace Ginfo\Parser\Sensor;

use Ginfo\Parser\ParserInterface;

final readonly class Mbmon implements ParserInterface
{
    /**
     * @return array{path: string|null, name: string, value: float, unit: string}[]|null
     */
    public function run(string $host = 'localhost', int $port = 411, int $timeout = 1): ?array
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
        $return = [];

        $lines = \explode("\n", \trim($data));
        foreach ($lines as $line) {
            if (1 === \preg_match('/(\w+)\s*:\s*([-+]?[\d\.]+)/i', $line, $match)) {
                $return[] = [
                    'path' => null,
                    'name' => $match[1],
                    'value' => (float) $match[2],
                    'unit' => 'C', // todo
                ];
            }
        }

        return $return;
    }
}
