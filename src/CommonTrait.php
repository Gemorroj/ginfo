<?php

namespace Ginfo;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
trait CommonTrait
{
    /**
     * Get a file's contents, or default to second param.
     */
    protected static function getContents(string $file, ?string $default = null): ?string
    {
        if (\file_exists($file) && \is_readable($file)) {
            $data = @\file_get_contents($file);
            if (false === $data) {
                return $default;
            }

            return \trim($data);
        }

        return $default;
    }

    /**
     * Locate an executable in the given paths, falling back to the bare name (PATH lookup).
     *
     * @param string[] $paths
     */
    protected static function locateExecutable(string $name, array $paths): string
    {
        foreach ($paths as $path) {
            if (\is_executable($path)) {
                return $path;
            }
        }

        return $name;
    }

    /**
     * @param non-empty-string $delimiter
     *
     * @return array<string, string>
     */
    protected static function parseKeyValueBlock(string $block, string $delimiter = ':'): array
    {
        $tmp = [];
        foreach (\explode("\n", $block) as $line) {
            if (\str_contains($line, $delimiter)) {
                [$key, $value] = \explode($delimiter, $line, 2);
                $tmp[\trim($key)] = \trim($value);
            }
        }

        return $tmp;
    }

    /**
     * @return array{
     *     pid: int,
     *     master: bool,
     *     VmPeak: float|null,
     *     VmSize: float|null,
     *     uptime: int|null,
     * }[]
     */
    protected static function processStat(string $processName, ?string $cwd = null, int $timeout = 1): array
    {
        $out = [];
        $process = new Process(['pidof', $processName], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
            $pids = [];
            foreach (\explode(' ', \trim($process->getOutput())) as $pid) {
                if ('' !== $pid) {
                    $pids[] = (int) $pid;
                }
            }
            \sort($pids, \SORT_NUMERIC);
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            $pids = [];
        }

        if ($pids) {
            $masterPid = $pids[0];
            foreach ($pids as $pid) {
                $pidProcess = [
                    'pid' => $pid,
                    'master' => $pid === $masterPid,
                    'VmPeak' => null,
                    'VmSize' => null,
                    'uptime' => null,
                ];
                // https://man7.org/linux/man-pages/man5/proc_pid_status.5.html
                $pidStatus = self::getContents('/proc/'.$pid.'/status');
                if ($pidStatus) {
                    $pidStatusInfo = self::parsePidStatus($pidStatus);
                    $pidProcess['VmPeak'] = $pidStatusInfo['VmPeak'];
                    $pidProcess['VmSize'] = $pidStatusInfo['VmSize'];
                }

                $process = new Process(['ps', '-p', (string) $pid, '-o', 'etimes='], $cwd, ['LANG' => 'C'], null, (float) $timeout);
                try {
                    $process->mustRun();
                    $pidProcess['uptime'] = self::parseUptime($process->getOutput());
                } catch (ProcessFailedException|ProcessStartFailedException $e) {
                    // ignore
                }

                $out[] = $pidProcess;
            }
        }

        return $out;
    }

    /**
     * @return array{VmPeak: float|null, VmSize: float|null}
     */
    protected static function parsePidStatus(string $status): array
    {
        $result = [
            'VmPeak' => null,
            'VmSize' => null,
        ];

        foreach (self::parseKeyValueBlock($status, ':') as $key => $value) {
            if ('VmPeak' === $key || 'VmSize' === $key) {
                $result[$key] = (float) \explode(' ', $value)[0] * 1024; // always Kb
            }
        }

        return $result;
    }

    protected static function parseUptime(string $output): ?int
    {
        $output = \trim($output);

        return '' === $output ? null : (int) $output;
    }
}
