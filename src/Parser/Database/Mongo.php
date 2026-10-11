<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Mongo implements ParserInterface
{
    /**
     * @return array{
     *     serverStatus: array{
     *         host: string,
     *         version: string,
     *         process: string,
     *         pid: int,
     *         uptime: int|float,
     *         localTime: \DateTimeImmutable,
     *         page_faults: int|float,
     *         network: array{
     *             bytesIn: int|float,
     *             bytesOut: int|float,
     *             numRequests: int|float,
     *         },
     *         counters: array{
     *             insert: int|float,
     *             query: int|float,
     *             update: int|float,
     *             delete: int|float,
     *             getmore: int|float,
     *             command: int|float,
     *         },
     *         connections: array{
     *             current: int|float,
     *             available: int|float,
     *             totalCreated: int|float,
     *             rejected: int|float,
     *             active: int|float,
     *             threaded: int|float,
     *             exhaustIsMaster: int|float,
     *             exhaustHello: int|float,
     *             awaitingTopologyChanges: int|float,
     *             loadBalanced: int|float,
     *         },
     *     },
     *     databases: array<string, array{
     *         sizeOnDisk: int|float,
     *         empty: bool,
     *         stats: array{
     *             db: string,
     *             collections: int,
     *             views: int,
     *             objects: int,
     *             avgObjSize: int|float,
     *             dataSize: int|float,
     *             storageSize: int|float,
     *             indexes: int,
     *             indexSize: int|float,
     *             totalSize: int|float,
     *             scaleFactor: int,
     *             fsUsedSize: int|float,
     *             fsTotalSize: int|float,
     *             ok: int,
     *         },
     *         top: array<string, array{
     *             total: array{time: int|float, count: int|float},
     *             readLock: array{time: int|float, count: int|float},
     *             writeLock: array{time: int|float, count: int|float},
     *             queries: array{time: int|float, count: int|float},
     *             getmore: array{time: int|float, count: int|float},
     *             insert: array{time: int|float, count: int|float},
     *             update: array{time: int|float, count: int|float},
     *             remove: array{time: int|float, count: int|float},
     *             commands: array{time: int|float, count: int|float},
     *         }>
     *     }>
     * }
     */
    public function run(\MongoDB\Driver\Manager $connection = new \MongoDB\Driver\Manager('mongodb://127.0.0.1:27017')): array
    {
        $result = [
            'serverStatus' => [],
            'databases' => [],
        ];

        $cursorServerStatus = $connection->executeCommand('admin', new \MongoDB\Driver\Command(['serverStatus' => 1]));
        $serverStatus = \current($cursorServerStatus->toArray());
        $result['serverStatus']['host'] = (string) $serverStatus->host;
        $result['serverStatus']['version'] = (string) $serverStatus->version;
        $result['serverStatus']['process'] = (string) $serverStatus->process;
        $result['serverStatus']['pid'] = (int) $serverStatus->pid;
        $result['serverStatus']['uptime'] = (int) $serverStatus->uptime;
        $localTime = $serverStatus->localTime->toDateTimeImmutable();
        $result['serverStatus']['localTime'] = $localTime instanceof \DateTimeImmutable ? $localTime : new \DateTimeImmutable();
        $result['serverStatus']['page_faults'] = (int) $serverStatus->extra_info->page_faults;

        $result['serverStatus']['network']['bytesIn'] = (int) $serverStatus->network->bytesIn;
        $result['serverStatus']['network']['bytesOut'] = (int) $serverStatus->network->bytesOut;
        $result['serverStatus']['network']['numRequests'] = (int) $serverStatus->network->numRequests;

        $result['serverStatus']['counters']['insert'] = (int) $serverStatus->opcounters->insert;
        $result['serverStatus']['counters']['query'] = (int) $serverStatus->opcounters->query;
        $result['serverStatus']['counters']['update'] = (int) $serverStatus->opcounters->update;
        $result['serverStatus']['counters']['delete'] = (int) $serverStatus->opcounters->delete;
        $result['serverStatus']['counters']['getmore'] = (int) $serverStatus->opcounters->getmore;
        $result['serverStatus']['counters']['command'] = (int) $serverStatus->opcounters->command;

        $result['serverStatus']['connections']['current'] = (int) $serverStatus->connections->current;
        $result['serverStatus']['connections']['available'] = (int) $serverStatus->connections->available;
        $result['serverStatus']['connections']['totalCreated'] = (int) $serverStatus->connections->totalCreated;
        $result['serverStatus']['connections']['rejected'] = (int) $serverStatus->connections->rejected;
        $result['serverStatus']['connections']['active'] = (int) $serverStatus->connections->active;
        $result['serverStatus']['connections']['threaded'] = (int) $serverStatus->connections->threaded;
        $result['serverStatus']['connections']['exhaustIsMaster'] = (int) $serverStatus->connections->exhaustIsMaster;
        $result['serverStatus']['connections']['exhaustHello'] = (int) $serverStatus->connections->exhaustHello;
        $result['serverStatus']['connections']['awaitingTopologyChanges'] = (int) $serverStatus->connections->awaitingTopologyChanges;
        $result['serverStatus']['connections']['loadBalanced'] = (int) $serverStatus->connections->loadBalanced;

        $cursorTop = $connection->executeCommand('admin', new \MongoDB\Driver\Command(['top' => 1]));
        $top = \current($cursorTop->toArray());
        $topData = [];
        foreach ($top->totals as $key => $items) {
            if ('note' === $key) {
                continue;
            }
            $items = (array) $items;
            $topData[$key] = [
                'total' => self::castTopItem($items['total'] ?? null),
                'readLock' => self::castTopItem($items['readLock'] ?? null),
                'writeLock' => self::castTopItem($items['writeLock'] ?? null),
                'queries' => self::castTopItem($items['queries'] ?? null),
                'getmore' => self::castTopItem($items['getmore'] ?? null),
                'insert' => self::castTopItem($items['insert'] ?? null),
                'update' => self::castTopItem($items['update'] ?? null),
                'remove' => self::castTopItem($items['remove'] ?? null),
                'commands' => self::castTopItem($items['commands'] ?? null),
            ];
        }

        $cursorListDatabases = $connection->executeCommand('admin', new \MongoDB\Driver\Command(['listDatabases' => 1]));
        $listDatabases = \current($cursorListDatabases->toArray());
        foreach ($listDatabases->databases as $item) {
            $dbName = (string) $item->name;
            $cursorDbstats = $connection->executeCommand($dbName, new \MongoDB\Driver\Command(['dbstats' => 1]));
            $dbstats = (array) \current($cursorDbstats->toArray());

            $top = [];
            foreach ($topData as $key => $topItem) {
                if (\str_starts_with($key, $dbName.'.')) {
                    $key = \substr($key, \strlen($dbName) + 1);
                    $top[$key] = $topItem;
                }
            }

            $result['databases'][$dbName] = [
                'sizeOnDisk' => (int) $item->sizeOnDisk,
                'empty' => (bool) $item->empty,
                'stats' => [
                    'db' => (string) ($dbstats['db'] ?? ''),
                    'collections' => (int) ($dbstats['collections'] ?? 0),
                    'views' => (int) ($dbstats['views'] ?? 0),
                    'objects' => (int) ($dbstats['objects'] ?? 0),
                    'avgObjSize' => (int) ($dbstats['avgObjSize'] ?? 0),
                    'dataSize' => (int) ($dbstats['dataSize'] ?? 0),
                    'storageSize' => (int) ($dbstats['storageSize'] ?? 0),
                    'indexes' => (int) ($dbstats['indexes'] ?? 0),
                    'indexSize' => (int) ($dbstats['indexSize'] ?? 0),
                    'totalSize' => (int) ($dbstats['totalSize'] ?? 0),
                    'scaleFactor' => (int) ($dbstats['scaleFactor'] ?? 0),
                    'fsUsedSize' => (int) ($dbstats['fsUsedSize'] ?? 0),
                    'fsTotalSize' => (int) ($dbstats['fsTotalSize'] ?? 0),
                    'ok' => (int) ($dbstats['ok'] ?? 0),
                ],
                'top' => $top,
            ];
        }

        return $result;
    }

    /**
     * @return array{time: int, count: int}
     */
    private static function castTopItem(mixed $item): array
    {
        $item = (array) $item;

        return [
            'time' => (int) ($item['time'] ?? 0),
            'count' => (int) ($item['count'] ?? 0),
        ];
    }
}
