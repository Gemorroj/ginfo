<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Mongo;
use PHPUnit\Framework\TestCase;

final class MongoTest extends TestCase
{
    public function testMongo(): void
    {
        $connection = new \MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
        $connection->executeCommand('ginfo', new \MongoDB\Driver\Command([
            'create' => 'cappedCollection',
            'capped' => true,
            'size' => 1000,
        ]));

        $data = (new Mongo())->run($connection);

        self::assertFalse($data['databases']['ginfo']['empty']);
        self::assertNotEmpty($data['databases']['ginfo']['sizeOnDisk']);
        self::assertNotEmpty($data['databases']['ginfo']['top']['cappedCollection']['total']);
        self::assertNotEmpty($data['databases']['ginfo']['stats']['totalSize']);

        self::assertNotEmpty($data['serverStatus']['version']);
        self::assertNotEmpty($data['serverStatus']['network']['numRequests']);
        self::assertNotEmpty($data['serverStatus']['counters']['command']);
        self::assertNotEmpty($data['serverStatus']['connections']['available']);
    }
}
