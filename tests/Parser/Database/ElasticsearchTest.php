<?php

namespace Ginfo\Tests\Parser\Database;

use Ginfo\Parser\Database\Elasticsearch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

final class ElasticsearchTest extends TestCase
{
    public function testElasticsearch(): void
    {
        $httpClient = HttpClient::create([
            'verify_peer' => false,
        ]);
        $data = (new Elasticsearch())->run('https://127.0.0.1:9200/_cluster/stats', 'admin', 'Qdfg!_13dZ', $httpClient);

        self::assertNotEmpty($data['stats']['cluster_name']);
        self::assertNotEmpty($data['stats']['nodes']['count']);
        self::assertNotEmpty($data['stats']['indices']['count']);
    }
}
