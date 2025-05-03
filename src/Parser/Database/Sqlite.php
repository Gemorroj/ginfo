<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Sqlite implements ParserInterface
{
    /**
     * @return array{
     *     sqlite_version: string,
     *     sqlite_source_id: string,
     *     db_size: float|int,
     *     pragma: array{
     *         auto_vacuum: int,
     *         automatic_index: int,
     *         busy_timeout: int,
     *         cache_size: int,
     *         encoding: string,
     *         ignore_check_constraints: int,
     *         incremental_vacuum: bool,
     *         journal_mode: string,
     *         journal_size_limit: int,
     *         locking_mode: string,
     *         page_count: int,
     *         page_size: int,
     *         quick_check: string,
     *         read_uncommitted: int,
     *         secure_delete: int,
     *         synchronous: int,
     *         threads: int,
     *         trusted_schema: int,
     *         wal_autocheckpoint: int,
     *         collation_list: string[],
     *         compile_options: string[],
     *         table_list: array{
     *             schema: string,
     *             name: string,
     *             type: string,
     *             ncol: int,
     *             wr: int,
     *             strict: int,
     *         }[],
     *     },
     * }|null
     */
    public function run(\PDO $connection = new \PDO('sqlite::memory:')): ?array
    {
        $result = [
            'sqlite_version' => '',
            'sqlite_source_id' => '',
            'db_size' => 0,
            'pragma' => [],
        ];

        $query = $connection->query('SELECT sqlite_version()');
        $result['sqlite_version'] = $query->fetchColumn();

        $query = $connection->query('SELECT sqlite_source_id()');
        $result['sqlite_source_id'] = $query->fetchColumn();

        $pragmaScalar = [
            'auto_vacuum',
            'automatic_index',
            'busy_timeout',
            'cache_size',
            'encoding',
            'ignore_check_constraints',
            'incremental_vacuum',
            'journal_mode',
            'journal_size_limit',
            'locking_mode',
            'page_count',
            'page_size',
            'quick_check',
            'read_uncommitted',
            'secure_delete',
            'synchronous',
            'threads',
            'trusted_schema',
            'wal_autocheckpoint',
        ];
        foreach ($pragmaScalar as $item) {
            $query = $connection->query('PRAGMA '.$item);
            $result['pragma'][$item] = $query->fetchColumn();
        }

        $query = $connection->query('PRAGMA collation_list');
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result['pragma']['collation_list'][] = $row['name'];
        }
        $query = $connection->query('PRAGMA compile_options');
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result['pragma']['compile_options'][] = $row['compile_options'];
        }
        $query = $connection->query('PRAGMA table_list');
        foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result['pragma']['table_list'][] = [
                'schema' => $row['schema'],
                'name' => $row['name'],
                'type' => $row['type'],
                'ncol' => $row['ncol'],
                'wr' => $row['wr'],
                'strict' => $row['strict'],
            ];
        }

        $result['db_size'] = $result['pragma']['page_size'] * $result['pragma']['page_count'];

        return $result;
    }
}
