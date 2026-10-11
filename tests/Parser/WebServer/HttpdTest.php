<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Parser\WebServer\Httpd;
use PHPUnit\Framework\TestCase;

final class HttpdTest extends TestCase
{
    public function testHttpd(): void
    {
        $data = (new Httpd())->run('apache2', 'http://localhost/server-status');
        if (!$data) {
            self::markTestSkipped('Httpd is not found');
        }

        self::assertStringStartsWith('2.4.', $data['version']);
        self::assertStringStartsWith('-D', $data['args']);
        self::assertMatchesRegularExpression('/^\d+\.\d+ \d+\.\d+ \d+\.\d+$/', $data['status']['load']);
        self::assertNotEmpty($data['processes']);
        foreach ($data['processes'] as $process) {
            self::assertIsNumeric($process['VmPeak']);
            self::assertIsNumeric($process['VmSize']);
            self::assertIsNumeric($process['uptime']);
        }
    }
}
