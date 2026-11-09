<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Ginfo;
use Ginfo\Parser\Database\Mysql;
use PHPUnit\Framework\TestCase;

final class MysqlTest extends TestCase
{
    public function testMysql(): void
    {
        try {
            $connection = new \PDO('mysql:host=127.0.0.1', 'root', '');
        } catch (\PDOException $e) {
            self::markTestSkipped('Mysql/MariaDB is not found');
        }

        $connection->exec('CREATE DATABASE IF NOT EXISTS ginfo_test');
        $connection->exec('CREATE TABLE IF NOT EXISTS ginfo_test.data_length (id INT PRIMARY KEY)');
        $connection->exec('CREATE OR REPLACE VIEW ginfo_test.data_length_view AS SELECT id FROM ginfo_test.data_length');

        $data = (new Mysql())->run($connection);

        self::assertNotEmpty($data['global_status']);
        self::assertNotEmpty($data['global_variables']);
        self::assertNotEmpty($data['performance_95th_percentile']);
        self::assertNotEmpty($data['count_queries']);
        self::assertNotEmpty($data['data_length']);

        // views have NULL data_length/index_length in information_schema and must not leak NULL to the DTO
        $mysql = (new Ginfo())->getMysql($connection);
        self::assertNotEmpty($mysql->getDataLength());
    }
}
