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
     * }
     */
    public function run(\PDO $connection = new \PDO('sqlite::memory:')): array
    {
        $result = [
            'sqlite_version' => '',
            'sqlite_source_id' => '',
            'db_size' => 0,
            'pragma' => [],
        ];

        $query = $connection->query('SELECT sqlite_version()');
        if (false !== $query) {
            $result['sqlite_version'] = (string) $query->fetchColumn();
        }

        $query = $connection->query('SELECT sqlite_source_id()');
        if (false !== $query) {
            $result['sqlite_source_id'] = (string) $query->fetchColumn();
        }

        $result['pragma'] = [
            'auto_vacuum' => (int) self::fetchPragma($connection, 'auto_vacuum'),
            'automatic_index' => (int) self::fetchPragma($connection, 'automatic_index'),
            'busy_timeout' => (int) self::fetchPragma($connection, 'busy_timeout'),
            'cache_size' => (int) self::fetchPragma($connection, 'cache_size'),
            'encoding' => (string) self::fetchPragma($connection, 'encoding'),
            'ignore_check_constraints' => (int) self::fetchPragma($connection, 'ignore_check_constraints'),
            'incremental_vacuum' => (bool) self::fetchPragma($connection, 'incremental_vacuum'),
            'journal_mode' => (string) self::fetchPragma($connection, 'journal_mode'),
            'journal_size_limit' => (int) self::fetchPragma($connection, 'journal_size_limit'),
            'locking_mode' => (string) self::fetchPragma($connection, 'locking_mode'),
            'page_count' => (int) self::fetchPragma($connection, 'page_count'),
            'page_size' => (int) self::fetchPragma($connection, 'page_size'),
            'quick_check' => (string) self::fetchPragma($connection, 'quick_check'),
            'read_uncommitted' => (int) self::fetchPragma($connection, 'read_uncommitted'),
            'secure_delete' => (int) self::fetchPragma($connection, 'secure_delete'),
            'synchronous' => (int) self::fetchPragma($connection, 'synchronous'),
            'threads' => (int) self::fetchPragma($connection, 'threads'),
            'trusted_schema' => (int) self::fetchPragma($connection, 'trusted_schema'),
            'wal_autocheckpoint' => (int) self::fetchPragma($connection, 'wal_autocheckpoint'),
            'collation_list' => [],
            'compile_options' => [],
            'table_list' => [],
        ];

        $query = $connection->query('PRAGMA collation_list');
        if (false !== $query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['pragma']['collation_list'][] = (string) $row['name'];
            }
        }
        $query = $connection->query('PRAGMA compile_options');
        if (false !== $query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['pragma']['compile_options'][] = (string) $row['compile_options'];
            }
        }
        $query = $connection->query('PRAGMA table_list');
        if (false !== $query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['pragma']['table_list'][] = [
                    'schema' => (string) $row['schema'],
                    'name' => (string) $row['name'],
                    'type' => (string) $row['type'],
                    'ncol' => (int) $row['ncol'],
                    'wr' => (int) $row['wr'],
                    'strict' => (int) $row['strict'],
                ];
            }
        }

        $result['db_size'] = $result['pragma']['page_size'] * $result['pragma']['page_count'];

        return $result;
    }

    private static function fetchPragma(\PDO $connection, string $name): mixed
    {
        $query = $connection->query('PRAGMA '.$name);

        return false !== $query ? $query->fetchColumn() : null;
    }
}
