<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Caddy implements ParserInterface
{
    /**
     * @param string|null $configPage uri for config page http://localhost:2019/config/ for example. see https://caddyserver.com/docs/api#get-configpath
     *
     * @return array{
     *     version: string,
     *     build_info: array{go: string, path: string, mod: string, dep: string[], build: string[]},
     *     list_modules: string[],
     *     config: array|null
     * }|null
     */
    public function run(?string $configPage = null, ?string $cwd = null): ?array
    {
        $res = [
            'version' => '',
            'build_info' => [],
            'list_modules' => [],
            'config' => null,
        ];

        // version
        $process = new Process(['caddy', 'version'], $cwd, ['LANG' => 'C']);
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
            $configPageContent = @\file_get_contents($configPage);
            if (false !== $configPageContent) {
                try {
                    $res['config'] = \json_decode($configPageContent, true, 512, \JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    // ignore
                }
            }
        }

        return $res;
    }
}
