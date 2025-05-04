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
     *         usagePageFileMB: int,
     *         totalPageFileMB: int,
     *         availPageFileMB: int,
     *         ramMB: int,
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
     * }|null
     */
    public function run(\MongoDB\Driver\Manager $connection = new \MongoDB\Driver\Manager('mongodb://127.0.0.1:27017')): ?array
    {
        $result = [
            'serverStatus' => [],
            'databases' => [],
        ];

        $cursorServerStatus = $connection->executeCommand('admin', new \MongoDB\Driver\Command(['serverStatus' => 1]));
        $serverStatus = \current($cursorServerStatus->toArray());
        $result['serverStatus']['host'] = $serverStatus->host;
        $result['serverStatus']['version'] = $serverStatus->version;
        $result['serverStatus']['process'] = $serverStatus->process;
        $result['serverStatus']['pid'] = $serverStatus->pid;
        $result['serverStatus']['uptime'] = $serverStatus->uptime;
        $result['serverStatus']['localTime'] = $serverStatus->localTime->toDateTimeImmutable();
        \var_dump($serverStatus->extra_info);
        $result['serverStatus']['page_faults'] = $serverStatus->extra_info->page_faults;
        $result['serverStatus']['usagePageFileMB'] = $serverStatus->extra_info->usagePageFileMB;
        $result['serverStatus']['totalPageFileMB'] = $serverStatus->extra_info->totalPageFileMB;
        $result['serverStatus']['availPageFileMB'] = $serverStatus->extra_info->availPageFileMB;
        $result['serverStatus']['ramMB'] = $serverStatus->extra_info->ramMB;

        $result['serverStatus']['network']['bytesIn'] = $serverStatus->network->bytesIn;
        $result['serverStatus']['network']['bytesOut'] = $serverStatus->network->bytesOut;
        $result['serverStatus']['network']['numRequests'] = $serverStatus->network->numRequests;

        $result['serverStatus']['counters']['insert'] = $serverStatus->opcounters->insert;
        $result['serverStatus']['counters']['query'] = $serverStatus->opcounters->query;
        $result['serverStatus']['counters']['update'] = $serverStatus->opcounters->update;
        $result['serverStatus']['counters']['delete'] = $serverStatus->opcounters->delete;
        $result['serverStatus']['counters']['getmore'] = $serverStatus->opcounters->getmore;
        $result['serverStatus']['counters']['command'] = $serverStatus->opcounters->command;

        $result['serverStatus']['connections']['current'] = $serverStatus->connections->current;
        $result['serverStatus']['connections']['available'] = $serverStatus->connections->available;
        $result['serverStatus']['connections']['totalCreated'] = $serverStatus->connections->totalCreated;
        $result['serverStatus']['connections']['rejected'] = $serverStatus->connections->rejected;
        $result['serverStatus']['connections']['active'] = $serverStatus->connections->active;
        $result['serverStatus']['connections']['threaded'] = $serverStatus->connections->threaded;
        $result['serverStatus']['connections']['exhaustIsMaster'] = $serverStatus->connections->exhaustIsMaster;
        $result['serverStatus']['connections']['exhaustHello'] = $serverStatus->connections->exhaustHello;
        $result['serverStatus']['connections']['awaitingTopologyChanges'] = $serverStatus->connections->awaitingTopologyChanges;
        $result['serverStatus']['connections']['loadBalanced'] = $serverStatus->connections->loadBalanced;

        $cursorTop = $connection->executeCommand('admin', new \MongoDB\Driver\Command(['top' => 1]));
        $top = \current($cursorTop->toArray());
        $topData = [];
        foreach ($top->totals as $key => $items) {
            if ('note' === $key) {
                continue;
            }
            $items = (array) $items;
            $topData[$key] = \array_map(static function (\stdClass $item): array {
                return (array) $item;
            }, $items);
        }

        $cursorListDatabases = $connection->executeCommand('admin', new \MongoDB\Driver\Command(['listDatabases' => 1]));
        $listDatabases = \current($cursorListDatabases->toArray());
        foreach ($listDatabases->databases as $item) {
            $cursorDbstats = $connection->executeCommand($item->name, new \MongoDB\Driver\Command(['dbstats' => 1]));
            $dbstats = (array) \current($cursorDbstats->toArray());

            $top = [];
            foreach ($topData as $key => $topItem) {
                if (\str_starts_with($key, $item->name.'.')) {
                    $key = \substr($key, \strlen($item->name) + 1);
                    $top[$key] = $topItem;
                }
            }

            $result['databases'][$item->name] = [
                'sizeOnDisk' => $item->sizeOnDisk,
                'empty' => $item->empty,
                'stats' => $dbstats,
                'top' => $top,
            ];
        }

        return $result;
    }
}
