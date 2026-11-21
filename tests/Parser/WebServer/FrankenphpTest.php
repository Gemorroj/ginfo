<?php

namespace Ginfo\Tests\Parser\WebServer;

use Ginfo\Ginfo;
use Ginfo\Parser\WebServer\Frankenphp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FrankenphpTest extends TestCase
{
    public function testFrankenphp(): void
    {
        $threadsUrl = self::getThreadsUrl();
        $data = (new Frankenphp())->run($threadsUrl);
        if (!$data) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertStringStartsWith('FrankenPHP', $data['version']);
        self::assertNotEmpty($data['build_info']['go']);
        self::assertNotEmpty($data['build_info']['path']);
        self::assertNotEmpty($data['build_info']['dep']);
        self::assertNotEmpty($data['build_info']['build']);

        self::assertNotEmpty($data['list_modules']);
        self::assertContains('frankenphp', $data['list_modules']);
        self::assertContains('http.handlers.php', $data['list_modules']);
        self::assertContains('admin.api.frankenphp', $data['list_modules']);

        self::assertNotEmpty($data['processes']);
        foreach ($data['processes'] as $process) {
            self::assertGreaterThan(0, $process['pid']);
            if (null !== $process['VmPeak']) {
                self::assertIsNumeric($process['VmPeak']);
            }
            if (null !== $process['VmSize']) {
                self::assertIsNumeric($process['VmSize']);
            }
            self::assertNotNull($process['uptime']);
            self::assertIsInt($process['uptime']);
        }

        if (!$data['threads']) {
            if (null !== self::getConfiguredThreadsUrl()) {
                self::fail('FrankenPHP admin API is not available at '.$threadsUrl);
            }
            self::markTestSkipped('FrankenPHP admin API is not available at '.$threadsUrl);
        }

        self::assertNotEmpty($data['threads']);
        self::assertGreaterThanOrEqual(0, $data['reserved_thread_count']);
        foreach ($data['threads'] as $thread) {
            self::assertGreaterThanOrEqual(0, $thread['index']);
            self::assertNotEmpty($thread['name']);
            self::assertNotEmpty($thread['state']);
            self::assertGreaterThanOrEqual(0, $thread['waiting_since_milliseconds']);
            self::assertGreaterThanOrEqual(0, $thread['request_started_at']);
            self::assertGreaterThanOrEqual(0, $thread['request_count']);
            self::assertGreaterThanOrEqual(0, $thread['memory_usage']);
        }
    }

    public function testGinfo(): void
    {
        $threadsUrl = self::getThreadsUrl();
        $ginfo = new Ginfo();
        $frankenphp = $ginfo->getFrankenphp($threadsUrl);
        if (!$frankenphp) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertStringStartsWith('FrankenPHP', $frankenphp->getVersion());
        self::assertNotEmpty($frankenphp->getBuildInfo()->getGo());
        self::assertNotEmpty($frankenphp->getBuildInfo()->getPath());
        self::assertNotEmpty($frankenphp->getBuildInfo()->getMod());
        self::assertNotEmpty($frankenphp->getBuildInfo()->getDep());
        self::assertNotEmpty($frankenphp->getBuildInfo()->getBuild());
        self::assertContains('frankenphp', $frankenphp->getListModules());
        self::assertNotEmpty($frankenphp->getProcesses());

        $process = $frankenphp->getProcesses()[0];
        self::assertGreaterThan(0, $process->getPid());
        if (null !== $process->getVmPeak()) {
            self::assertIsNumeric($process->getVmPeak());
        }
        if (null !== $process->getVmSize()) {
            self::assertIsNumeric($process->getVmSize());
        }
        self::assertNotNull($process->getUptime());
        self::assertIsInt($process->getUptime());

        if (!$frankenphp->getThreads()) {
            if (null !== self::getConfiguredThreadsUrl()) {
                self::fail('FrankenPHP admin API is not available at '.$threadsUrl);
            }
            self::markTestSkipped('FrankenPHP admin API is not available at '.$threadsUrl);
        }
        self::assertNotEmpty($frankenphp->getThreads());
        self::assertGreaterThanOrEqual(0, $frankenphp->getReservedThreadCount());

        $thread = $frankenphp->getThreads()[0];
        self::assertGreaterThanOrEqual(0, $thread->getIndex());
        self::assertNotEmpty($thread->getName());
        self::assertNotEmpty($thread->getState());
        self::assertGreaterThanOrEqual(0, $thread->getWaitingSinceMilliseconds());
        self::assertGreaterThanOrEqual(0, $thread->getRequestStartedAt());
        self::assertGreaterThanOrEqual(0, $thread->getRequestCount());
        self::assertGreaterThanOrEqual(0, $thread->getMemoryUsage());
    }

    public function testMockThreads(): void
    {
        $httpClient = new MockHttpClient(new MockResponse(\json_encode([
            'ThreadDebugStates' => [
                [
                    'Index' => 1,
                    'Name' => 'worker.php',
                    'State' => 'ready',
                    'IsWaiting' => false,
                    'IsBusy' => true,
                    'WaitingSinceMilliseconds' => 0,
                    'CurrentURI' => '/worker',
                    'CurrentMethod' => 'POST',
                    'RequestStartedAt' => 1795272459919,
                    'RequestCount' => 7,
                    'MemoryUsage' => 1024,
                ],
            ],
            'ReservedThreadCount' => 3,
        ], \JSON_THROW_ON_ERROR)));

        $data = (new Frankenphp())->run('http://frankenphp/threads', null, $httpClient);
        if (!$data) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertSame(3, $data['reserved_thread_count']);
        self::assertSame([
            [
                'index' => 1,
                'name' => 'worker.php',
                'state' => 'ready',
                'is_waiting' => false,
                'is_busy' => true,
                'waiting_since_milliseconds' => 0,
                'current_uri' => '/worker',
                'current_method' => 'POST',
                'request_started_at' => 1795272459919,
                'request_count' => 7,
                'memory_usage' => 1024,
            ],
        ], $data['threads']);
    }

    public function testGinfoWithMockThreads(): void
    {
        $httpClient = new MockHttpClient(new MockResponse(\json_encode([
            'ThreadDebugStates' => [
                [
                    'Index' => 1,
                    'Name' => 'worker.php',
                    'State' => 'ready',
                    'IsWaiting' => false,
                    'IsBusy' => true,
                    'WaitingSinceMilliseconds' => 0,
                    'CurrentURI' => '/worker',
                    'CurrentMethod' => 'POST',
                    'RequestStartedAt' => 1795272459919,
                    'RequestCount' => 7,
                    'MemoryUsage' => 1024,
                ],
            ],
            'ReservedThreadCount' => 3,
        ], \JSON_THROW_ON_ERROR)));

        $ginfo = new Ginfo();
        $frankenphp = $ginfo->getFrankenphp('http://frankenphp/threads', null, $httpClient);
        if (!$frankenphp) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertCount(1, $frankenphp->getThreads());
        self::assertSame(3, $frankenphp->getReservedThreadCount());

        $thread = $frankenphp->getThreads()[0];
        self::assertSame(1, $thread->getIndex());
        self::assertSame('worker.php', $thread->getName());
        self::assertSame('ready', $thread->getState());
        self::assertFalse($thread->isWaiting());
        self::assertTrue($thread->isBusy());
        self::assertSame(0, $thread->getWaitingSinceMilliseconds());
        self::assertSame('/worker', $thread->getCurrentUri());
        self::assertSame('POST', $thread->getCurrentMethod());
        self::assertSame(1795272459919, $thread->getRequestStartedAt());
        self::assertSame(7, $thread->getRequestCount());
        self::assertSame(1024, $thread->getMemoryUsage());
    }

    public function testMockThreadsWithoutReservedCount(): void
    {
        $httpClient = new MockHttpClient(new MockResponse(\json_encode([
            'ThreadDebugStates' => [
                ['Index' => 0],
            ],
        ], \JSON_THROW_ON_ERROR)));

        $data = (new Frankenphp())->run('http://frankenphp/threads', null, $httpClient);
        if (!$data) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertCount(1, $data['threads']);
        self::assertSame(0, $data['reserved_thread_count']);
    }

    public function testAdminApiUnavailable(): void
    {
        $httpClient = new MockHttpClient(new MockResponse('error', ['http_code' => 500]));

        $data = (new Frankenphp())->run('http://frankenphp/threads', null, $httpClient);
        if (!$data) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertNotEmpty($data['version']);
        self::assertNotEmpty($data['list_modules']);
        self::assertSame([], $data['threads']);
        self::assertSame(0, $data['reserved_thread_count']);

        $ginfo = new Ginfo();
        $frankenphp = $ginfo->getFrankenphp('http://frankenphp/threads', null, new MockHttpClient(new MockResponse('error', ['http_code' => 500])));
        if (!$frankenphp) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertSame([], $frankenphp->getThreads());
        self::assertSame(0, $frankenphp->getReservedThreadCount());
    }

    public static function provideDisabledThreadsUrls(): \Generator
    {
        yield 'null' => [null];
        yield 'empty string' => [''];
    }

    #[DataProvider('provideDisabledThreadsUrls')]
    public function testThreadsUrlDisabled(?string $threadsUrl): void
    {
        $httpClient = new MockHttpClient(new MockResponse(\json_encode([
            'ThreadDebugStates' => [
                ['Index' => 1, 'Name' => 'should-not-be-used'],
            ],
            'ReservedThreadCount' => 5,
        ], \JSON_THROW_ON_ERROR)));

        $data = (new Frankenphp())->run($threadsUrl, null, $httpClient);
        if (!$data) {
            self::markTestSkipped('FrankenPHP is not found');
        }

        self::assertSame([], $data['threads']);
        self::assertSame(0, $data['reserved_thread_count']);
    }

    private static function getThreadsUrl(): string
    {
        return self::getConfiguredThreadsUrl() ?? 'http://localhost:2019/frankenphp/threads';
    }

    private static function getConfiguredThreadsUrl(): ?string
    {
        $threadsUrl = \getenv('FRANKENPHP_THREADS_URL');
        if (false === $threadsUrl || '' === $threadsUrl) {
            return null;
        }

        return $threadsUrl;
    }
}
