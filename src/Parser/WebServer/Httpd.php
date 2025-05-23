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

        $res = [
            'version' => '',
            'loaded' => [],
            'mpm' => '',
            'threaded' => false,
            'forked' => false,
            'args' => '',
            'processes' => [],
            'status' => null,
        ];
        $lines = \explode("\n", \trim($process->getOutput()));
        foreach ($lines as $line) {
            $line = \trim($line);

            if (\str_starts_with($line, 'Server version: Apache/')) {
                $res['version'] = \str_replace('Server version: Apache/', '', $line);
            }
            if (\str_starts_with($line, 'Server loaded: ')) {
                $str = \str_replace('Server loaded: ', '', $line);
                $res['loaded'] = \array_map('trim', \explode(',', $str));
            }
            if (\str_starts_with($line, 'Server MPM: ')) {
                $res['mpm'] = \trim(\str_replace('Server MPM:     ', '', $line));
            }
            if (\str_contains($line, 'threaded: ')) {
                $res['threaded'] = \str_contains($line, 'yes');
            }
            if (\str_contains($line, 'forked: ')) {
                $res['forked'] = \str_contains($line, 'yes');
            }
            if (\str_starts_with($line, '-D ')) {
                $res['args'] .= $res['args'] ? ' '.$line : $line;
            }
        }

        $res['processes'] = self::processStat($processName, $cwd, $timeout);

        if ($statusPage) {
            $httpClient ??= HttpClient::create(['timeout' => (float) $timeout]);
            try {
                $statusPageContent = $httpClient->request('GET', $statusPage)->getContent();
                $statusPageLines = \explode("\n", \trim($statusPageContent));
                foreach ($statusPageLines as $line) {
                    if (\str_starts_with($line, '<dt>Server uptime:')) {
                        $res['status']['uptime'] = \trim(\str_replace(['<dt>', '</dt>', 'Server uptime:'], '', $line));
                    }
                    if (\str_starts_with($line, '<dt>Server load:')) {
                        $res['status']['load'] = \trim(\str_replace(['<dt>', '</dt>', 'Server load:'], '', $line));
                    }
                    if (\str_contains($line, 'Total accesses:')) {
                        if (\preg_match('/Total accesses:\s+(\d+)/', $line, $matches)) {
                            $res['status']['total_accesses'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, 'Total Traffic:')) {
                        if (\preg_match('/Total Traffic:\s+([0-9.]+)\s+([a-zA-Z]+)/', $line, $matches)) {
                            $res['status']['total_traffic'] = ['value' => (float) $matches[1], 'unit' => $matches[2]];
                        }
                    }
                    if (\str_contains($line, 'Total Duration:')) {
                        if (\preg_match('/Total Duration:\s+(\d+)/', $line, $matches)) {
                            $res['status']['total_duration'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' requests/sec')) {
                        if (\preg_match('/([0-9.]+)\s+requests\/sec/', $line, $matches)) {
                            $res['status']['requests_sec'] = (float) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' B/second')) {
                        if (\preg_match('/(\d+)\s+B\/second/', $line, $matches)) {
                            $res['status']['b_second'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' B/request')) {
                        if (\preg_match('/(\d+)\s+B\/request/', $line, $matches)) {
                            $res['status']['b_request'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' ms/request')) {
                        if (\preg_match('/([0-9.]+)\s+ms\/request/', $line, $matches)) {
                            $res['status']['ms_request'] = (float) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' requests currently being processed')) {
                        if (\preg_match('/(\d+)\s+requests currently being processed/', $line, $matches)) {
                            $res['status']['requests_currently_processed'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' workers gracefully restarting')) {
                        if (\preg_match('/(\d+)\s+workers gracefully restarting/', $line, $matches)) {
                            $res['status']['workers_gracefully_restarting'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, ' idle workers')) {
                        if (\preg_match('/(\d+)\s+idle workers/', $line, $matches)) {
                            $res['status']['idle_workers'] = (int) $matches[1];
                        }
                    }
                    if (\str_contains($line, 'cache type:')) {
                        if (\preg_match('/cache type:\s+<b>([a-zA-Z0-9]+)<\/b>/', $line, $matches)) {
                            $res['status']['ssl_cache_type'] = $matches[1];
                        }
                    }
                    if (\str_contains($line, 'shared memory:')) {
                        if (\preg_match('/shared memory:\s+<b>(\d+)<\/b>\s+([a-zA-Z]+),/', $line, $matches)) {
                            $res['status']['ssl_shared_memory'] = ['value' => (int) $matches[1], 'unit' => $matches[2]];
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $res;
    }
}
