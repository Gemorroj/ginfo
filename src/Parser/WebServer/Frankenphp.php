<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\CommonTrait;
use Ginfo\Parser\ParserInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Frankenphp implements ParserInterface
{
    use CommonTrait;

    /**
     * @param string|null $threadsUrl uri for the FrankenPHP threads page http://localhost:2019/frankenphp/threads for example. see https://frankenphp.dev/docs/metrics/#threads-state-endpoint
     * @param string|null $cwd        The working directory or null to use the working dir of the current PHP process
     *
     * @return array{
     *     version: string,
     *     build_info: array{go: string, path: string, mod: string, dep: string[], build: string[]},
     *     list_modules: string[],
     *     processes: array{
     *          pid: int,
     *          master: bool,
     *          VmPeak: float|null,
     *          VmSize: float|null,
     *          uptime: int|null,
     *      }[],
     *     threads: array{
     *          index: int,
     *          name: string,
     *          state: string,
     *          is_waiting: bool,
     *          is_busy: bool,
     *          waiting_since_milliseconds: int,
     *          current_uri: string,
     *          current_method: string,
     *          request_started_at: int,
     *          request_count: int,
     *          memory_usage: int,
     *      }[],
     *     reserved_thread_count: int,
     * }|null
     */
    public function run(
        ?string $threadsUrl = 'http://localhost:2019/frankenphp/threads',
        ?string $cwd = null,
        ?HttpClientInterface $httpClient = null,
        int $timeout = 1,
    ): ?array {
        $res = [
            'version' => '',
            'build_info' => ['go' => '', 'path' => '', 'mod' => '', 'dep' => [], 'build' => []],
            'list_modules' => [],
            'processes' => [],
            'threads' => [],
            'reserved_thread_count' => 0,
        ];

        // version
        $process = new Process(['frankenphp', 'version'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }
        $res['version'] = \trim($process->getOutput());

        // build-info
        $process = new Process(['frankenphp', 'build-info'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }
        $res['build_info'] = self::parseBuildInfo($process->getOutput());

        // list-modules
        $process = new Process(['frankenphp', 'list-modules'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }
        $res['list_modules'] = self::parseListModules($process->getOutput());

        $res['processes'] = self::processStat('frankenphp', $cwd, $timeout);

        if ($threadsUrl) {
            $httpClient ??= HttpClient::create(['timeout' => (float) $timeout]);
            try {
                $threads = $httpClient->request('GET', $threadsUrl)->toArray();
                $res['threads'] = self::parseThreads($threads);
                $res['reserved_thread_count'] = (int) ($threads['ReservedThreadCount'] ?? 0);
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $res;
    }

    /**
     * @return array{go: string, path: string, mod: string, dep: string[], build: string[]}
     */
    private static function parseBuildInfo(string $output): array
    {
        $result = [
            'go' => '',
            'path' => '',
            'mod' => '',
            'dep' => [],
            'build' => [],
        ];

        foreach (\explode("\n", \trim($output)) as $line) {
            $parts = \explode("\t", \trim($line), 2);
            if (!isset($parts[1])) {
                continue;
            }

            $key = \trim($parts[0]);
            $value = \trim($parts[1]);
            if ('go' === $key || 'path' === $key || 'mod' === $key) {
                $result[$key] = $value;
            }
            if ('dep' === $key || 'build' === $key) {
                $result[$key][] = $value;
            }
        }

        return $result;
    }

    /**
     * The output contains both standard and non-standard modules, separated by blank lines and headings.
     * Do not stop at the first blank line: FrankenPHP-specific modules are listed after it.
     *
     * @return string[]
     */
    private static function parseListModules(string $output): array
    {
        $modules = [];
        foreach (\explode("\n", $output) as $line) {
            $line = \trim($line);
            if (!\preg_match('/^[a-z0-9][a-z0-9._-]*$/i', $line)) {
                continue;
            }

            $modules[] = $line;
        }

        return $modules;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array{
     *     index: int,
     *     name: string,
     *     state: string,
     *     is_waiting: bool,
     *     is_busy: bool,
     *     waiting_since_milliseconds: int,
     *     current_uri: string,
     *     current_method: string,
     *     request_started_at: int,
     *     request_count: int,
     *     memory_usage: int,
     * }[]
     */
    private static function parseThreads(array $data): array
    {
        $states = $data['ThreadDebugStates'] ?? [];
        if (!\is_array($states)) {
            return [];
        }

        $threads = [];
        foreach ($states as $state) {
            if (!\is_array($state)) {
                continue;
            }

            $threads[] = [
                'index' => (int) ($state['Index'] ?? 0),
                'name' => (string) ($state['Name'] ?? ''),
                'state' => (string) ($state['State'] ?? ''),
                'is_waiting' => (bool) ($state['IsWaiting'] ?? false),
                'is_busy' => (bool) ($state['IsBusy'] ?? false),
                'waiting_since_milliseconds' => (int) ($state['WaitingSinceMilliseconds'] ?? 0),
                'current_uri' => (string) ($state['CurrentURI'] ?? ''),
                'current_method' => (string) ($state['CurrentMethod'] ?? ''),
                'request_started_at' => (int) ($state['RequestStartedAt'] ?? 0),
                'request_count' => (int) ($state['RequestCount'] ?? 0),
                'memory_usage' => (int) ($state['MemoryUsage'] ?? 0),
            ];
        }

        return $threads;
    }
}
