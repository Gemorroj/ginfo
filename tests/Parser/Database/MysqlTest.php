<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Mysql;
use PHPUnit\Framework\TestCase;

final class MysqlTest extends TestCase
{
    public function testMysql(): void
    {
        // try {
        $connection = new \PDO('mysql:host=127.0.0.1', 'root', '');
        // } catch (\PDOException $e) {
        // self::markTestSkipped('Mysql/MariaDB is not found');
        // }

        $data = (new Mysql())->run($connection);

        self::assertNotEmpty($data['global_status']);
        self::assertNotEmpty($data['global_variables']);
        self::assertNotEmpty($data['performance_95th_percentile']);
        self::assertNotEmpty($data['count_queries']);
        self::assertNotEmpty($data['data_length']);
    }
}
