<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Sqlite;
use PHPUnit\Framework\TestCase;

final class SqliteTest extends TestCase
{
    public function testSqlite(): void
    {
        $connection = new \PDO('sqlite::memory:');
        $connection->exec('CREATE TABLE t1 (id INTEGER PRIMARY KEY, clmn1 TEXT, clmn2 TEXT, clmn3 TEXT)');
        $connection->exec('INSERT INTO t1 (clmn1, clmn2, clmn3) VALUES ("1some str1", "2some str1", "3some str1"), ("3some str2", "4some str2", "5some str2")');
        $connection->exec('CREATE INDEX t_idx1 ON t1 (clmn1)');
        $connection->exec('CREATE INDEX t_idx2 ON t1 (clmn2)');
        $connection->exec('CREATE INDEX t_idx3 ON t1 (clmn3)');
        $connection->exec('CREATE TABLE t2 (id INTEGER PRIMARY KEY, t TEXT)');
        $connection->exec('INSERT INTO t2 (t) VALUES ("some str1"), ("some str2")');

        $data = (new Sqlite())->run($connection);

        self::assertNotEmpty($data['sqlite_version']);
        self::assertNotEmpty($data['sqlite_source_id']);
        self::assertNotEmpty($data['db_size']);
        self::assertNotEmpty($data['pragma']['table_list']);
    }
}
