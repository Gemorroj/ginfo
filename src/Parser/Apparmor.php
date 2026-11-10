<?php

namespace Ginfo\Parser;

use Ginfo\CommonTrait;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Apparmor implements ParserInterface
{
    use CommonTrait;

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{
     *     enabled: bool,
     *     profiles: array<string, string>,
     *     processes: array<int, array{executable: string, profile: string, pid: int, status: string}>,
     * }|null
     */
    public function run(?string $cwd = null, int $timeout = 1): ?array
    {
        $binary = self::locateExecutable('aa-status', ['/usr/sbin/aa-status', '/sbin/aa-status', '/usr/bin/aa-status']);

        $json = self::execute([$binary, '--json'], $cwd, $timeout);
        if (null !== $json) {
            $data = \json_decode($json, true);
            if (\is_array($data)) {
                return self::parseJson($data);
            }
        }

        $text = self::execute([$binary], $cwd, $timeout);
        if (null !== $text) {
            return self::parseText($text);
        }

        // aa-status is not installed, but the module state is still readable
        $enabled = self::getContents('/sys/module/apparmor/parameters/enabled');
        if (null !== $enabled) {
            return [
                'enabled' => 'Y' === $enabled,
                'profiles' => [],
                'processes' => [],
            ];
        }

        return null;
    }

    /**
     * @param string[] $command
     *
     * @return string|null stdout or null when the process cannot be started
     */
    private static function execute(array $command, ?string $cwd, int $timeout): ?string
    {
        $process = new Process($command, $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->run();
        } catch (ProcessStartFailedException $e) {
            return null;
        }

        return $process->isStarted() ? $process->getOutput() : null;
    }

    /**
     * @param array<mixed> $data decoded "aa-status --json" output (AppArmor 3+)
     *
     * @return array{
     *     enabled: bool,
     *     profiles: array<string, string>,
     *     processes: array<int, array{executable: string, profile: string, pid: int, status: string}>,
     * }
     */
    private static function parseJson(array $data): array
    {
        $profiles = [];
        foreach (($data['profiles'] ?? []) as $name => $mode) {
            if (\is_string($name) && \is_string($mode)) {
                $profiles[$name] = $mode;
            }
        }

        $processes = [];
        foreach (($data['processes'] ?? []) as $executable => $items) {
            if (!\is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                if (!\is_array($item)) {
                    continue;
                }

                $processes[] = [
                    'executable' => (string) $executable,
                    'profile' => (string) ($item['profile'] ?? ''),
                    'pid' => (int) ($item['pid'] ?? 0),
                    'status' => (string) ($item['status'] ?? ''),
                ];
            }
        }

        return [
            'enabled' => true,
            'profiles' => $profiles,
            'processes' => $processes,
        ];
    }

    /**
     * @return array{
     *     enabled: bool,
     *     profiles: array<string, string>,
     *     processes: array<int, array{executable: string, profile: string, pid: int, status: string}>,
     * }
     */
    private static function parseText(string $output): array
    {
        $profiles = [];
        $processes = [];
        $enabled = true;
        $section = null;
        $mode = null;

        foreach (\explode("\n", $output) as $line) {
            $trimmed = \trim($line);
            if ('' === $trimmed) {
                continue;
            }

            if (\str_starts_with($trimmed, 'apparmor module is not loaded')) {
                $enabled = false;
                $section = $mode = null;
                continue;
            }

            if (1 === \preg_match('/^\d+ profiles are in ([\w-]+) mode\.$/', $trimmed, $matches)) {
                $section = 'profiles';
                $mode = $matches[1];
                continue;
            }
            if (1 === \preg_match('/^\d+ processes are in ([\w-]+) mode\.$/', $trimmed, $matches)) {
                $section = 'processes';
                $mode = $matches[1];
                continue;
            }
            if (\str_starts_with($trimmed, 'Out of ') && \str_ends_with($trimmed, 'processes have profiles defined.')) {
                $section = null;
                $mode = null;
                continue;
            }
            if (1 === \preg_match('/^\d+ (?:profiles are loaded|processes have profiles defined)\.$/', $trimmed)) {
                $section = null;
                $mode = null;
                continue;
            }
            if (1 === \preg_match('/^\d+ processes are unconfined but have a profile defined\.$/', $trimmed)) {
                $section = 'processes';
                $mode = 'unconfined';
                continue;
            }

            if (!\str_starts_with($line, '   ')) {
                $section = null;
                $mode = null;
                continue;
            }

            if ('profiles' === $section) {
                $profiles[$trimmed] = (string) $mode;
            } elseif ('processes' === $section && 1 === \preg_match('/^(\S+)\s+\((\d+)\)(?:\s+(.+))?$/', $trimmed, $matches)) {
                $processes[] = [
                    'executable' => $matches[1],
                    'profile' => '' !== ($matches[3] ?? '') ? $matches[3] : $matches[1],
                    'pid' => (int) $matches[2],
                    'status' => (string) $mode,
                ];
            }
        }

        return [
            'enabled' => $enabled,
            'profiles' => $profiles,
            'processes' => $processes,
        ];
    }
}
