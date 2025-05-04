<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Elasticsearch;
use PHPUnit\Framework\TestCase;

final class ElasticsearchTest extends TestCase
{
    public function testElasticsearch(): void
    {
        $data = (new Elasticsearch())->run('https://127.0.0.1:9200/_cluster/stats', 'admin', 'Qdfg!_13dZ');

        self::assertNotEmpty($data['stats']['cluster_name']);
        self::assertNotEmpty($data['stats']['nodes']['count']);
        self::assertNotEmpty($data['stats']['indices']['count']);
    }
}
