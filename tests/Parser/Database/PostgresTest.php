<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Postgres;
use PHPUnit\Framework\TestCase;

final class PostgresTest extends TestCase
{
    public function testPostgres(): void
    {
        try {
            $connection = new \PDO('pgsql:host=127.0.0.1', 'postgres', 'postgres');
        } catch (\PDOException $e) {
            self::markTestSkipped('Postgres is not found');
        }

        $data = (new Postgres())->run($connection);

        self::assertNotEmpty($data['version']);
        self::assertNotEmpty($data['pg_stat_activity']);
        self::assertNotEmpty($data['pg_stat_database']);
        self::assertNotEmpty($data['pg_stat_all_tables']);
        self::assertNotEmpty($data['pg_stat_all_indexes']);
        self::assertArrayHasKey('pg_stat_statements', $data);
    }
}
