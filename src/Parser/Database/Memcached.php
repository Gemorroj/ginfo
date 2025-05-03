<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Memcached implements ParserInterface
{
    /**
     * @return array{
     *     stats: array<string, array<string, string|int|float>>,
     *     stats_settings: array<string, array<string, string|int|float>>,
     *     stats_conns: array<string, array<string, string|int|float>>,
     * }|null
     */
    public function run(\Memcached $connection = new \Memcached()): ?array
    {
        return [
            'stats' => $connection->getStats() ?: [],
            'stats_settings' => $connection->getStats('settings') ?: [],
            'stats_conns' => $connection->getStats('conns') ?: [],
        ];
    }
}
