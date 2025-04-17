<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Mysql;
use PHPUnit\Framework\TestCase;

final class MysqlTest extends TestCase
{
    public function testMysql(): void
    {
        // my local config
        try {
            $connection = new \PDO('mysql:host=127.0.0.1', 'root', '');
        } catch (\PDOException $e) {
            self::markTestSkipped('Mysql is not found');
        }

        $data = (new Mysql())->run($connection);

        self::assertNotEmpty($data['global_status']);
        self::assertNotEmpty($data['variables']);
    }
}
