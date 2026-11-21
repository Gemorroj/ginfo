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
            $systemUptime = self::parseSystemUptime(self::getContents('/proc/uptime') ?? '');
            $clkTck = null !== $systemUptime ? self::getClkTck($cwd, $timeout) : 0;

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

                // https://man7.org/linux/man-pages/man5/proc_pid_stat.5.html
                $pidStat = self::getContents('/proc/'.$pid.'/stat');
                if (null !== $pidStat && null !== $systemUptime) {
                    $pidProcess['uptime'] = self::parseProcessUptime($pidStat, $systemUptime, $clkTck);
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

    /**
     * @return float|null seconds since boot
     */
    private static function parseSystemUptime(string $uptime): ?float
    {
        $parts = \preg_split('/\s+/', \trim($uptime));
        if (!\is_array($parts) || !isset($parts[0]) || !\is_numeric($parts[0])) {
            return null;
        }

        return (float) $parts[0];
    }

    /**
     * @return float|null start time in clock ticks since boot
     */
    private static function parsePidStatStartTime(string $stat): ?float
    {
        $pos = \strrpos($stat, ')');
        if (false === $pos) {
            return null;
        }

        $fields = \preg_split('/\s+/', \trim(\substr($stat, $pos + 2)));
        if (!\is_array($fields) || !isset($fields[19]) || !\is_numeric($fields[19])) {
            return null;
        }

        return (float) $fields[19];
    }

    private static function parseProcessUptime(string $stat, float $systemUptime, int $clkTck): ?int
    {
        if ($clkTck <= 0) {
            return null;
        }

        $startTicks = self::parsePidStatStartTime($stat);
        if (null === $startTicks) {
            return null;
        }

        return \max(0, (int) ($systemUptime - $startTicks / $clkTck));
    }

    private static function getClkTck(?string $cwd, int $timeout): int
    {
        $process = new Process(['getconf', 'CLK_TCK'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
            $clkTck = (int) \trim($process->getOutput());
            if ($clkTck > 0) {
                return $clkTck;
            }
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            // ignore
        }

        return 100;
    }
}
