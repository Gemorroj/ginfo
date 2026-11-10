<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\CommonTrait;
use Ginfo\Parser\ParserInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Httpd implements ParserInterface
{
    use CommonTrait;

    /**
     * @param string|null $statusPage uri for status page http://localhost/status/ for example. see https://httpd.apache.org/docs/current/mod/mod_status.html
     * @param string|null $cwd        The working directory or null to use the working dir of the current PHP process
     *
     * @return array{
     *     version: string,
     *     loaded: string[],
     *     mpm: string,
     *     threaded: bool,
     *     forked: bool,
     *     args: string,
     *     processes: array{
     *          pid: int,
     *          master: bool,
     *          VmPeak: float|null,
     *          VmSize: float|null,
     *          uptime: int|null,
     *      }[],
     *     status: array{
     *          uptime: string,
     *          load: string,
     *          total_accesses: int,
     *          total_traffic: array{value: float, unit: string},
     *          total_duration: int,
     *          requests_sec: float,
     *          b_second: int,
     *          b_request: int,
     *          ms_request: float,
     *          requests_currently_processed: int,
     *          workers_gracefully_restarting: int,
     *          idle_workers: int,
     *          ssl_cache_type: string,
     *          ssl_shared_memory: array{value: int, unit: string},
     *     }|null
     * }|null
     */
    public function run(string $processName = 'httpd', ?string $statusPage = null, ?string $cwd = null, ?HttpClientInterface $httpClient = null, int $timeout = 1): ?array
    {
        $process = new Process([$processName, '-V'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $res = self::parseVersion($process->getOutput());
        $res['processes'] = self::processStat($processName, $cwd, $timeout);
        $res['status'] = null;

        if ($statusPage) {
            $httpClient ??= HttpClient::create(['timeout' => (float) $timeout]);
            try {
                $res['status'] = self::parseStatus($httpClient->request('GET', $statusPage)->getContent());
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $res;
    }

    /**
     * @return array{
     *     version: string,
     *     loaded: string[],
     *     mpm: string,
     *     threaded: bool,
     *     forked: bool,
     *     args: string,
     * }
     */
    private static function parseVersion(string $output): array
    {
        $result = [
            'version' => '',
            'loaded' => [],
            'mpm' => '',
            'threaded' => false,
            'forked' => false,
            'args' => '',
        ];

        foreach (\explode("\n", \trim($output)) as $line) {
            $line = \trim($line);

            if (\str_starts_with($line, 'Server version: Apache/')) {
                $result['version'] = \str_replace('Server version: Apache/', '', $line);
            }
            if (\str_starts_with($line, 'Server loaded: ')) {
                $str = \str_replace('Server loaded: ', '', $line);
                $result['loaded'] = \array_map('trim', \explode(',', $str));
            }
            if (\str_starts_with($line, 'Server MPM: ')) {
                $result['mpm'] = \trim(\str_replace('Server MPM:', '', $line));
            }
            if (\str_contains($line, 'threaded: ')) {
                $result['threaded'] = \str_contains($line, 'yes');
            }
            if (\str_contains($line, 'forked: ')) {
                $result['forked'] = \str_contains($line, 'yes');
            }
            if (\str_starts_with($line, '-D ')) {
                $result['args'] .= $result['args'] ? ' '.$line : $line;
            }
        }

        return $result;
    }

    /**
     * @return array{
     *     uptime: string,
     *     load: string,
     *     total_accesses: int,
     *     total_traffic: array{value: float, unit: string},
     *     total_duration: int,
     *     requests_sec: float,
     *     b_second: int,
     *     b_request: int,
     *     ms_request: float,
     *     requests_currently_processed: int,
     *     workers_gracefully_restarting: int,
     *     idle_workers: int,
     *     ssl_cache_type: string,
     *     ssl_shared_memory: array{value: int, unit: string},
     * }
     */
    private static function parseStatus(string $content): array
    {
        $status = [
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
        ];

        foreach (\explode("\n", \trim($content)) as $line) {
            if (\str_starts_with($line, '<dt>Server uptime:')) {
                $status['uptime'] = \trim(\str_replace(['<dt>', '</dt>', 'Server uptime:'], '', $line));
            }
            if (\str_starts_with($line, '<dt>Server load:')) {
                $status['load'] = \trim(\str_replace(['<dt>', '</dt>', 'Server load:'], '', $line));
            }
            if (\str_contains($line, 'Total accesses:') && \preg_match('/Total accesses:\s+(\d+)/', $line, $matches)) {
                $status['total_accesses'] = (int) $matches[1];
            }
            if (\str_contains($line, 'Total Traffic:') && \preg_match('/Total Traffic:\s+([0-9.]+)\s+([a-zA-Z]+)/', $line, $matches)) {
                $status['total_traffic'] = ['value' => (float) $matches[1], 'unit' => $matches[2]];
            }
            if (\str_contains($line, 'Total Duration:') && \preg_match('/Total Duration:\s+(\d+)/', $line, $matches)) {
                $status['total_duration'] = (int) $matches[1];
            }
            if (\str_contains($line, ' requests/sec') && \preg_match('/([0-9.]+)\s+requests\/sec/', $line, $matches)) {
                $status['requests_sec'] = (float) $matches[1];
            }
            if (\preg_match('/([0-9.]+)\s+([kKmMgG]?[bB])\/second/', $line, $matches)) {
                $status['b_second'] = self::convertToBytes((float) $matches[1], $matches[2]);
            }
            if (\preg_match('/([0-9.]+)\s+([kKmMgG]?[bB])\/request/', $line, $matches)) {
                $status['b_request'] = self::convertToBytes((float) $matches[1], $matches[2]);
            }
            if (\str_contains($line, ' ms/request') && \preg_match('/([0-9.]+)\s+ms\/request/', $line, $matches)) {
                $status['ms_request'] = (float) $matches[1];
            }
            if (\str_contains($line, ' requests currently being processed') && \preg_match('/(\d+)\s+requests currently being processed/', $line, $matches)) {
                $status['requests_currently_processed'] = (int) $matches[1];
            }
            if (\str_contains($line, ' workers gracefully restarting') && \preg_match('/(\d+)\s+workers gracefully restarting/', $line, $matches)) {
                $status['workers_gracefully_restarting'] = (int) $matches[1];
            }
            if (\str_contains($line, ' idle workers') && \preg_match('/(\d+)\s+idle workers/', $line, $matches)) {
                $status['idle_workers'] = (int) $matches[1];
            }
            if (\str_contains($line, 'cache type:') && \preg_match('/cache type:\s+<b>([a-zA-Z0-9]+)<\/b>/', $line, $matches)) {
                $status['ssl_cache_type'] = $matches[1];
            }
            if (\str_contains($line, 'shared memory:') && \preg_match('/shared memory:\s+<b>(\d+)<\/b>\s+([a-zA-Z]+),/', $line, $matches)) {
                $status['ssl_shared_memory'] = ['value' => (int) $matches[1], 'unit' => $matches[2]];
            }
        }

        return $status;
    }

    /**
     * Converts a mod_status value like "10.2 kB/request" to bytes.
     */
    private static function convertToBytes(float $value, string $unit): int
    {
        $multiplier = match (\strtolower($unit)) {
            'kb' => 1024,
            'mb' => 1024 * 1024,
            'gb' => 1024 * 1024 * 1024,
            default => 1,
        };

        return (int) \round($value * $multiplier);
    }
}
