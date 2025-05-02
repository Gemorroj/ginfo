<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Manticore;
use PHPUnit\Framework\TestCase;

final class ManticoreTest extends TestCase
{
    public function testManticore(): void
    {
        try {
            $connection = new \PDO('mysql:host=127.0.0.1;port=9306', 'root', '');
        } catch (\PDOException $e) {
            self::markTestSkipped('Manticore is not found');
        }

        $data = (new Manticore())->run($connection);

        self::assertNotEmpty($data['global_variables']);
        self::assertNotEmpty($data['status']);
        self::assertNotEmpty($data['settings']);
        self::assertNotEmpty($data['agent_status']);
    }
}
