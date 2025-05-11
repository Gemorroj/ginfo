<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\CommonTrait;
use Ginfo\Parser\ParserInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Nginx implements ParserInterface
{
    use CommonTrait;

    /**
     * @param string|null $statusPage uri for json status page http://localhost/status/ for example. see https://nginx.org/en/docs/http/ngx_http_api_module.html
     * @param string|null $cwd        The working directory or null to use the working dir of the current PHP process
     *
     * @return array{
     *     nginx_version: string,
     *     crypto: string,
     *     tls_sni: bool,
     *     args: string,
     *     status: array|null,
     *     processes: array{
     *         pid: int,
     *         master: bool,
     *         VmPeak: float|null,
     *         VmSize: float|null,
     *         uptime: int|null,
     *     }[],
     * }|null
     */
    public function run(?string $statusPage = null, ?string $cwd = null, ?HttpClientInterface $httpClient = null, int $timeout = 1): ?array
    {
        $process = new Process(['nginx', '-V'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $res = [
            'nginx_version' => '',
            'crypto' => '',
            'tls_sni' => false,
            'args' => '',
            'status' => null,
            'processes' => [],
        ];
        $lines = \explode("\n", \trim($process->getErrorOutput())); // exactly use STDERR. see https://trac.nginx.org/nginx/ticket/592
        foreach ($lines as $line) {
            $line = \trim($line);

            if (\str_starts_with($line, 'nginx version: nginx/')) {
                $res['nginx_version'] = \str_replace('nginx version: nginx/', '', $line);
            }
            if (\str_starts_with($line, 'built with ')) {
                $res['crypto'] = \str_replace('built with ', '', $line);
            }
            if (\str_contains($line, 'TLS SNI support enabled')) {
                $res['tls_sni'] = true;
            }
            if (\str_contains($line, 'configure arguments: ')) {
                $res['args'] = \str_replace('configure arguments: ', '', $line);
            }
        }

        $process = new Process(['pidof', 'nginx'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
            $pids = \explode(' ', \trim($process->getOutput()));
            \sort($pids, \SORT_NUMERIC);
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            $pids = [];
        }

        if ($pids) {
            $masterPid = $pids[0];
            foreach ($pids as $pid) {
                $process = [
                    'pid' => $pid,
                    'master' => $pid === $masterPid,
                    'VmPeak' => null,
                    'VmSize' => null,
                    'uptime' => null,
                ];
                // https://man7.org/linux/man-pages/man5/proc_pid_status.5.html
                $pidStatus = self::getContents('/proc/'.$pid.'/status');
                if ($pidStatus) {
                    $keyValuePidStatus = self::parseKeyValueBlock($pidStatus, ':');
                    foreach ($keyValuePidStatus as $key => $value) {
                        if ('VmPeak' === $key) {
                            $valueBytes = \explode(' ', $value)[0] * 1024; // always Kb
                            $process['VmPeak'] = $valueBytes;
                            continue;
                        }
                        if ('VmSize' === $key) {
                            $valueBytes = \explode(' ', $value)[0] * 1024; // always Kb
                            $process['VmSize'] = $valueBytes;
                            continue;
                        }
                    }
                }

                $process = new Process(['ps', '-p', $pid, '-o', 'etimes='], $cwd, ['LANG' => 'C'], null, (float) $timeout);
                try {
                    $process->mustRun();
                    $process['uptime'] = (int) \trim($process->getOutput());
                } catch (ProcessFailedException|ProcessStartFailedException $e) {
                    // ignore
                }

                $res['processes'][] = $process;
            }
        }

        if ($statusPage) {
            $httpClient ??= HttpClient::create(['timeout' => (float) $timeout]);
            try {
                $res['status'] = $httpClient->request('GET', $statusPage)->toArray();
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $res;
    }
}
