<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Parser\WebServer\Angie;
use PHPUnit\Framework\TestCase;

final class AngieTest extends TestCase
{
    public function testAngie(): void
    {
        $data = (new Angie())->run('http://localhost/status/');
        if (!$data) {
            self::markTestSkipped('Angie is not found');
        }

        self::assertInstanceOf(\DateTimeImmutable::class, $data['build_date']);
        self::assertTrue($data['tls_sni']);
        self::assertStringContainsString('--with-http_stub_status_module', $data['args']);
        self::assertIsArray($data['status']);
        self::assertIsArray($data['processes']);
        foreach ($data['processes'] as $process) {
            self::assertIsNumeric($process['pid']);
            self::assertIsNumeric($process['VmPeak']);
            self::assertIsNumeric($process['VmSize']);
            self::assertIsNumeric($process['uptime']);
        }
    }
}
