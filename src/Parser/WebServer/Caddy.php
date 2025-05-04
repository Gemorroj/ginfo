<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Caddy implements ParserInterface
{
    /**
     * @param string|null $configPage uri for config page http://localhost:2019/config/ for example. see https://caddyserver.com/docs/api#get-configpath
     * @param string|null $cwd        The working directory or null to use the working dir of the current PHP process
     *
     * @return array{
     *     version: string,
     *     build_info: array{go: string, path: string, mod: string, dep: string[], build: string[]},
     *     list_modules: string[],
     *     config: array|null
     * }|null
     */
    public function run(?string $configPage = null, ?string $cwd = null, ?HttpClientInterface $httpClient = null, int $timeout = 1): ?array
    {
        $res = [
            'version' => '',
            'build_info' => [],
            'list_modules' => [],
            'config' => null,
        ];

        // version
        $process = new Process(['caddy', 'version'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }
        $res['version'] = \trim($process->getOutput());

        // build-info
        $process = new Process(['caddy', 'build-info'], $cwd, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }
        $lines = \explode("\n", \trim($process->getOutput()));
        foreach ($lines as $line) {
            $arr = \explode("\t", \trim($line), 2);
            $key = \trim($arr[0]);
            $value = \trim($arr[1]);
            if ('go' === $key || 'path' === $key || 'mod' === $key) {
                $res['build_info'][$key] = $value;
            }
            if ('dep' === $key || 'build' === $key) {
                $res['build_info'][$key][] = $value;
            }
        }

        // list-modules
        $process = new Process(['caddy', 'list-modules'], $cwd, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }
        $stopToken = '';
        $lines = \explode("\n", \trim($process->getOutput()));
        foreach ($lines as $line) {
            $line = \trim($line);
            if ($stopToken === $line) {
                break;
            }

            $res['list_modules'][] = $line;
        }

        if ($configPage) {
            $httpClient ??= HttpClient::create(['timeout' => (float) $timeout]);
            try {
                $res['config'] = $httpClient->request('GET', $configPage)->toArray();
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $res;
    }
}
