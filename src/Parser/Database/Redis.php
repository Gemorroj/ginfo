<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Redis implements ParserInterface
{
    /**
     * @return array<string, array<string, string>>
     */
    public function run(\Redis $connection = new \Redis(['host' => '127.0.0.1', 'port' => 6379])): array
    {
        $result = [
            'server' => [],
            'clients' => [],
            'memory' => [],
            'persistence' => [],
            'stats' => [],
            'replication' => [],
            'cpu' => [],
            'modules' => [],
            'errorstats' => [],
            'cluster' => [],
            'keyspace' => [],
            'keysizes' => [],
        ];

        /** @var string $response */
        $response = $connection->rawcommand('INFO');
        $lines = \explode("\r\n", $response);

        $key = '';
        foreach ($lines as $line) {
            if ('' === $line) {
                continue;
            }

            if ('#' === $line[0]) {
                $key = \strtolower(\trim(\substr($line, 1)));
                continue;
            }

            $values = \explode(':', \trim($line), 2);
            $result[$key][$values[0]] = $values[1];
        }

        return $result;
    }
}
