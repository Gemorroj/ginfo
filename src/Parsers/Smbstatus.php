<?php

namespace Ginfo\Parsers;

use Symfony\Component\Process\Process;

/**
 * Get info on a samba install by running smbstatus.
 */
class Smbstatus implements Parser
{
    public static function work(): ?array
    {
        $process = new Process(['smbstatus'], null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
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

    private static function parseService(string $service): array
    {
        $out = [
            'service' => null,
            'pid' => null,
            'machine' => null,
            'connectedAt' => null,
            'encryption' => null, // samba 4.4
            'signing' => null, // samba 4.4
        ];

        $service .= ' '; // fix for connectedAt parser algorithm
        $connectedAtYear = false;
        foreach (\preg_split('/\s+/', $service) as $token) {
            if (!isset($out['service'])) {
                $out['service'] = $token;
                continue;
            }
            if (!isset($out['pid'])) {
                $out['pid'] = $token;
                continue;
            }
            if (!isset($out['machine'])) {
                $out['machine'] = $token;
                continue;
            }
            if (!isset($out['connectedAt'])) {
                $out['connectedAt'] = $token;
                continue;
            }

            // tested date: Wed Dec  9 01:40:20 PM 2015 CET   or    Fri Mar 30 15:48:34 2018
            if (isset($out['connectedAt']) && !($out['connectedAt'] instanceof \DateTime)) {
                if ($connectedAtYear) { //perhaps timezone
                    if (false === \mb_strpos($token, '-')) { // yes, timezone
                        $out['connectedAt'] .= ' '.$token;
                        $out['connectedAt'] = new \DateTime($out['connectedAt']);
                        continue;
                    }
                    $out['connectedAt'] = new \DateTime($out['connectedAt']);
                } else {
                    if (\preg_match('/^[0-9]{4}$/', $token)) { //year
                        $connectedAtYear = true;
                    }
                    $out['connectedAt'] .= ' '.$token;
                    continue;
                }
            }
            if (!isset($out['encryption'])) {
                $out['encryption'] = '-' === $token ? null : $token;
                continue;
            }
            if (!isset($out['signing'])) {
                $out['signing'] = '-' === $token ? null : $token;
                continue;
            }
        }

        return $out;
    }

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
        foreach (\preg_split('/\s+/', $connection) as $token) {
            if (!isset($out['pid'])) {
                $out['pid'] = $token;
                continue;
            }
            if (!isset($out['user'])) {
                $out['user'] = $token;
                continue;
            }
            if (!isset($out['group'])) {
                $out['group'] = $token;
                continue;
            }
            if (!isset($out['host'])) {
                $out['host'] = $token;
                continue;
            }
            if (!isset($out['ip'])) {
                $out['ip'] = \trim($token, '()');
                continue;
            }
            if (!isset($out['protocolVersion'])) {
                $out['protocolVersion'] = $token;
                continue;
            }
            if (isset($out['protocolVersion']) && 'Unknown' === $out['protocolVersion']) {
                $out['protocolVersion'] .= ' '.$token;
                continue;
            }
            if (!isset($out['encryption'])) {
                $out['encryption'] = '-' === $token ? null : $token;
                continue;
            }
            if (!isset($out['signing'])) {
                $out['signing'] = '-' === $token ? null : $token;
                continue;
            }
        }

        return $out;
    }

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
        foreach (\preg_split('/\s+/', $file) as $token) {
            if (!isset($out['pid'])) {
                $out['pid'] = $token;
                continue;
            }
            if (!isset($out['uid'])) {
                $out['uid'] = $token;
                continue;
            }
            if (!isset($out['denyMode'])) {
                $out['denyMode'] = $token;
                continue;
            }
            if (!isset($out['access'])) {
                $out['access'] = $token;
                continue;
            }
            if (!isset($out['rw'])) {
                $out['rw'] = $token;
                continue;
            }
            if (!isset($out['oplock'])) {
                $out['oplock'] = $token;
                continue;
            }
            if (!isset($out['sharePath'])) {
                $out['sharePath'] = $token;
                continue;
            }
            if (!isset($out['name'])) {
                $out['name'] = $token;
                continue;
            }
            if (!isset($out['time'])) {
                $out['time'] = $token;
                continue;
            }
            if (isset($out['time']) && !($out['time'] instanceof \DateTime)) {
                $out['time'] .= ' '.$token; //add all strings to time
                continue;
            }
        }
        $out['time'] = new \DateTime($out['time']);

        return $out;
    }
}
