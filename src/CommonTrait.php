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
            $pids = \explode(' ', \trim($process->getOutput()));
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
                    $keyValuePidStatus = self::parseKeyValueBlock($pidStatus, ':');
                    foreach ($keyValuePidStatus as $key => $value) {
                        if ('VmPeak' === $key) {
                            $valueBytes = \explode(' ', $value)[0] * 1024; // always Kb
                            $pidProcess['VmPeak'] = $valueBytes;
                            continue;
                        }
                        if ('VmSize' === $key) {
                            $valueBytes = \explode(' ', $value)[0] * 1024; // always Kb
                            $pidProcess['VmSize'] = $valueBytes;
                            continue;
                        }
                    }
                }

                $process = new Process(['ps', '-p', $pid, '-o', 'etimes='], $cwd, ['LANG' => 'C'], null, (float) $timeout);
                try {
                    $process->mustRun();
                    $pidProcess['uptime'] = (int) \trim($process->getOutput());
                } catch (ProcessFailedException|ProcessStartFailedException $e) {
                    // ignore
                }

                $out[] = $pidProcess;
            }
        }

        return $out;
    }
}
