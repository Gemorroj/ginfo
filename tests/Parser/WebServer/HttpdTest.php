<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Parser\WebServer\Httpd;
use PHPUnit\Framework\TestCase;

final class HttpdTest extends TestCase
{
    public function testHttpd(): void
    {
        // my local config
        $data = (new Httpd())->run('http://localhost/openserver/server-status');
        if (!$data) {
            self::markTestSkipped('Httpd is not found');
        }

        self::assertStringStartsWith('2.4.', $data['version']);
        self::assertStringStartsWith('-D', $data['args']);
        self::assertStringStartsWith('-1.', $data['status']['load']);
    }
}
