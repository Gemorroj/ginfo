<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Parser\WebServer\Caddy;
use PHPUnit\Framework\TestCase;

final class CaddyTest extends TestCase
{
    public function testCaddy(): void
    {
        // my local config
        $data = Caddy::work('http://localhost:2019/config/');
        if (!$data) {
            self::markTestSkipped('Caddy is not found');
        }

        self::assertSame('caddy', $data['build_info']['path']);
        self::assertNotEmpty($data['build_info']['dep']);
        self::assertNotEmpty($data['build_info']['build']);
        self::assertNotEmpty($data['list_modules']);
        self::assertNotEmpty($data['config']);
    }
}
