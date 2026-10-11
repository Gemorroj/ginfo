<?php

namespace Ginfo\Parser;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * Get info on a samba install by running smbstatus.
 */
final readonly class Smbstatus implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{
     *     connections: array{pid: int|null, user: string|null, group: string|null, host: string|null, ip: string|null, protocolVersion: string|null, encryption: string|null, signing: string|null}[],
     *     services: array{pid: int|null, service: string|null, machine: string|null, connectedAt: \DateTimeImmutable|null, encryption: string|null, signing: string|null}[],
     *     files: array{pid: int|null, uid: int|null, denyMode: string|null, access: string|null, rw: string|null, oplock: string|null, sharePath: string|null, name: string|null, time: \DateTimeImmutable|null}[]
     * }|null
     */
    public function run(?string $cwd = null, int $timeout = 1): ?array
    {
        $process = new Process(['smbstatus'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $lines = \explode("\n", \trim($process->getOutput()));
        \array_shift($lines); // remove header

        $res = [
            'connections' => [],
            'services' => [],
            'files' => [],
        ];

        $currentLocation = null;
        foreach ($lines as $line) {
            $line = \trim($line);

            if ('' === $line) {
                $currentLocation = null;
                continue;
            }
            if ('-' === $line[0]) {
                continue;
            }

            if (\preg_match('/^PID\s+Username\s+Group\s+Machine/', $line)) { // Beginning connections list?
                $currentLocation = 'c';
                continue;
            }
            if (\preg_match('/^Service\s+pid\s+machine\s+Connected at/', $line)) { // Beginning services list?
                $currentLocation = 's';
                continue;
            }
            if (\preg_match('/^Pid\s+Uid\s+DenyMode\s+Access\s+R\/W\s+Oplock\s+SharePath\s+Name\s+Time/', $line)) { // Beginning locked files list?
                $currentLocation = 'f';
                continue;
            }

            if ('c' === $currentLocation) { // A connection?
                $res['connections'][] = self::parseConnection($line);
                continue;
            }
            if ('s' === $currentLocation) { // A service?
                $res['services'][] = self::parseService($line);
                continue;
            }
            if ('f' === $currentLocation) { // A locked file?
                $res['files'][] = self::parseFile($line);
                continue;
            }
        }

        return $res;
    }

    /**
     * @return array{pid: int|null, service: string|null, machine: string|null, connectedAt: \DateTimeImmutable|null, encryption: string|null, signing: string|null}
     */
    private static function parseService(string $service): array
    {
        $out = [
            'pid' => null,
            'service' => null,
            'machine' => null,
            'connectedAt' => null,
            'encryption' => null, // samba 4.4
            'signing' => null, // samba 4.4
        ];

        $tokens = \preg_split('/\s+/', \trim($service)) ?: [];
        if (isset($tokens[0])) {
            $out['service'] = $tokens[0];
        }
        if (isset($tokens[1])) {
            $out['pid'] = (int) $tokens[1];
        }
        if (isset($tokens[2])) {
            $out['machine'] = $tokens[2];
        }

        // tested date: Wed Dec  9 01:40:20 PM 2015 CET   or    Fri Mar 30 15:48:34 2018
        $rest = \implode(' ', \array_slice($tokens, 3));
        if (\preg_match('/^(\w+\s+\w+\s+\d+\s+\d+:\d+:\d+(?:\s+[AP]M)?\s+\d{4}(?:\s+[A-Z]{2,6}(?![-\w]))?)\s*(.*)$/', $rest, $match)) {
            $out['connectedAt'] = new \DateTimeImmutable($match[1]);
            $extra = '' !== $match[2] ? (\preg_split('/\s+/', \trim($match[2])) ?: []) : [];
            if (isset($extra[0])) {
                $out['encryption'] = '-' === $extra[0] ? null : $extra[0];
            }
            if (isset($extra[1])) {
                $out['signing'] = '-' === $extra[1] ? null : $extra[1];
            }
        }

        return $out;
    }

    /**
     * @return array{pid: int|null, user: string|null, group: string|null, host: string|null, ip: string|null, protocolVersion: string|null, encryption: string|null, signing: string|null}
     */
    private static function parseConnection(string $connection): array
    {
        $out = [
            'pid' => null,
            'user' => null,
            'group' => null,
            'host' => null,
            'ip' => null,
            'protocolVersion' => null, // samba 4
            'encryption' => null, // samba 4.4
            'signing' => null, // samba 4.4
        ];

        $tokens = \preg_split('/\s+/', \trim($connection)) ?: [];

        if (isset($tokens[0])) {
            $out['pid'] = (int) $tokens[0];
        }
        $out['user'] = $tokens[1] ?? null;
        $out['group'] = $tokens[2] ?? null;
        $out['host'] = $tokens[3] ?? null;
        $out['ip'] = isset($tokens[4]) ? \trim($tokens[4], '()') : null;

        $rest = \array_slice($tokens, 5);
        if (isset($rest[0])) {
            $out['protocolVersion'] = \array_shift($rest);
            if ('Unknown' === $out['protocolVersion'] && isset($rest[0])) {
                $out['protocolVersion'] .= ' '.\array_shift($rest);
            }
        }
        if (isset($rest[0])) {
            $token = \array_shift($rest);
            $out['encryption'] = '-' === $token ? null : $token;
        }
        if (isset($rest[0])) {
            $token = \array_shift($rest);
            $out['signing'] = '-' === $token ? null : $token;
        }

        return $out;
    }

    /**
     * @return array{pid: int|null, uid: int|null, denyMode: string|null, access: string|null, rw: string|null, oplock: string|null, sharePath: string|null, name: string|null, time: \DateTimeImmutable|null}
     */
    private static function parseFile(string $file): array
    {
        $out = [
            'pid' => null,
            'uid' => null,
            'denyMode' => null,
            'access' => null,
            'rw' => null,
            'oplock' => null,
            'sharePath' => null,
            'name' => null,
            'time' => null,
        ];

        $tokens = \preg_split('/\s+/', \trim($file)) ?: [];

        if (isset($tokens[0])) {
            $out['pid'] = (int) $tokens[0];
        }
        if (isset($tokens[1])) {
            $out['uid'] = (int) $tokens[1];
        }
        $out['denyMode'] = $tokens[2] ?? null;
        $out['access'] = $tokens[3] ?? null;
        $out['rw'] = $tokens[4] ?? null;
        $out['oplock'] = $tokens[5] ?? null;
        $out['sharePath'] = $tokens[6] ?? null;
        $out['name'] = $tokens[7] ?? null;

        $time = \implode(' ', \array_slice($tokens, 8));
        $out['time'] = '' !== $time ? new \DateTimeImmutable($time) : null;

        return $out;
    }
}
