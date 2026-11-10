<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Parser\WebServer\Httpd;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HttpdParseTest extends TestCase
{
    public static function provideVersion(): \Generator
    {
        yield 'apache 2.4 event' => [
            <<<'TXT'
                Server version: Apache/2.4.62 (Ubuntu)
                Server built:   2024-07-16T18:14:25
                Server's Module Magic Number: 20120211:130
                Server loaded:  APR 1.7.2, APR-UTIL 1.6.3, PCRE 10.42 2022-12-11
                Compiled using: APR 1.7.2, APR-UTIL 1.6.3
                Architecture:   64-bit
                Server MPM:     event
                  threaded:     yes (fixed thread count)
                    forked:     yes (variable process count)
                Server compiled with....
                 -D APR_HAS_SENDFILE
                 -D APR_HAS_MMAP
                 -D HTTPD_ROOT="/etc/apache2"
                TXT,
            [
                'version' => '2.4.62 (Ubuntu)',
                'loaded' => ['APR 1.7.2', 'APR-UTIL 1.6.3', 'PCRE 10.42 2022-12-11'],
                'mpm' => 'event',
                'threaded' => true,
                'forked' => true,
                'args' => '-D APR_HAS_SENDFILE -D APR_HAS_MMAP -D HTTPD_ROOT="/etc/apache2"',
            ],
        ];

        yield 'apache 2.4 prefork' => [
            <<<'TXT'
                Server version: Apache/2.4.41 (Debian)
                Server loaded:  APR 1.6.5, APR-UTIL 1.6.1, PCRE 8.39 2016-06-14
                Server MPM:     prefork
                  threaded:     no
                    forked:     yes (variable process count)
                 -D APR_HAS_SENDFILE
                TXT,
            [
                'version' => '2.4.41 (Debian)',
                'loaded' => ['APR 1.6.5', 'APR-UTIL 1.6.1', 'PCRE 8.39 2016-06-14'],
                'mpm' => 'prefork',
                'threaded' => false,
                'forked' => true,
                'args' => '-D APR_HAS_SENDFILE',
            ],
        ];

        yield 'empty' => [
            '',
            [
                'version' => '',
                'loaded' => [],
                'mpm' => '',
                'threaded' => false,
                'forked' => false,
                'args' => '',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideVersion')]
    public function testParseVersion(string $output, array $expected): void
    {
        $method = new \ReflectionMethod(Httpd::class, 'parseVersion');

        self::assertSame($expected, $method->invoke(null, $output));
    }

    public static function provideStatus(): \Generator
    {
        yield 'default mod_status' => [
            <<<'TXT'
                <dl><dt>Server Version: Apache/2.4.62 (Ubuntu)</dt>
                <dt>Server MPM: event</dt>
                <dt>Server Built: 2024-07-16T18:14:25</dt>
                <dt>Current Time: Tuesday, 10-Nov-2026 12:00:00 UTC</dt>
                <dt>Restart Time: Tuesday, 10-Nov-2026 11:00:00 UTC</dt>
                <dt>Server uptime: 1 hour 36 minutes 20 seconds</dt>
                <dt>Server load: 0.11 0.17 0.18</dt>
                <dt>Total accesses: 12345 - Total Traffic: 123.4 MB</dt>
                <dt>Total Duration: 987654</dt>
                <dt>.045 requests/sec - 478 B/second - 10.2 kB/request - 0.5 ms/request</dt>
                <dt>2 requests currently being processed, 8 idle workers</dt>
                <dt>0 workers gracefully restarting</dt>
                <dt>cache type: <b>SHMCB</b>, shared memory: <b>512000</b> bytes, current entries: 5</dt>
                </dl>
                TXT,
            [
                'uptime' => '1 hour 36 minutes 20 seconds',
                'load' => '0.11 0.17 0.18',
                'total_accesses' => 12345,
                'total_traffic' => ['value' => 123.4, 'unit' => 'MB'],
                'total_duration' => 987654,
                'requests_sec' => 0.045,
                'b_second' => 478,
                'b_request' => 10445,
                'ms_request' => 0.5,
                'requests_currently_processed' => 2,
                'workers_gracefully_restarting' => 0,
                'idle_workers' => 8,
                'ssl_cache_type' => 'SHMCB',
                'ssl_shared_memory' => ['value' => 512000, 'unit' => 'bytes'],
            ],
        ];

        yield 'kilobytes and megabytes' => [
            <<<'TXT'
                <dt>.10 requests/sec - 2.6 kB/second - 1.5 MB/request</dt>
                TXT,
            [
                'uptime' => '',
                'load' => '',
                'total_accesses' => 0,
                'total_traffic' => ['value' => 0.0, 'unit' => ''],
                'total_duration' => 0,
                'requests_sec' => 0.1,
                'b_second' => 2662,
                'b_request' => 1572864,
                'ms_request' => 0.0,
                'requests_currently_processed' => 0,
                'workers_gracefully_restarting' => 0,
                'idle_workers' => 0,
                'ssl_cache_type' => '',
                'ssl_shared_memory' => ['value' => 0, 'unit' => ''],
            ],
        ];

        yield 'empty' => [
            '',
            [
                'uptime' => '',
                'load' => '',
                'total_accesses' => 0,
                'total_traffic' => ['value' => 0.0, 'unit' => ''],
                'total_duration' => 0,
                'requests_sec' => 0.0,
                'b_second' => 0,
                'b_request' => 0,
                'ms_request' => 0.0,
                'requests_currently_processed' => 0,
                'workers_gracefully_restarting' => 0,
                'idle_workers' => 0,
                'ssl_cache_type' => '',
                'ssl_shared_memory' => ['value' => 0, 'unit' => ''],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideStatus')]
    public function testParseStatus(string $content, array $expected): void
    {
        $method = new \ReflectionMethod(Httpd::class, 'parseStatus');

        self::assertSame($expected, $method->invoke(null, $content));
    }
}
