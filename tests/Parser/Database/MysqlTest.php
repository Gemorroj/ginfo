<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Mysql;
use PHPUnit\Framework\TestCase;

final class MysqlTest extends TestCase
{
    public function testMysql(): void
    {
        // my local config
        $connection = new \PDO('mysql:host=127.0.0.1', 'root', '');
        $data = (new Mysql())->run($connection);
        if (!$data) {
            self::markTestSkipped('Mysql is not found');
        }

        self::assertNotEmpty($data['global_status']);
        self::assertNotEmpty($data['variables']);
    }
}
