<?php

namespace Ginfo\Parser;

use Ginfo\Common;

final readonly class Mdadm implements ParserInterface
{
    /**
     * @return array{device: string, status: string, level: string, drives: array{path: string, state: string}[], size: float, count: array{active: int, total: int}, chart: string}[]|null
     */
    public function run(): ?array
    {
        /*
Personalities : [raid1]
md0 : active raid1 sdb[0]
      1046528 blocks super 1.2 [2/1] [U_]
         */
        $mdadmContents = Common::getContents('/proc/mdstat');
        if (null === $mdadmContents) {
            return null;
        }

        if (false === \preg_match_all('/(?P<device>\S+)\s*:\s*(?P<state>\w+)(?P<blurb>\s+\([^)]+\))?\s*raid(?P<level>\d+)\s*(?P<drives>[\w+\[\d+\] (\(\w\))?]+)\n\s+(?P<blocks>\d+) blocks[^[]+\[(?P<counts>\d\/\d)\] \[(?P<chart>[U\_]+)\]/mi', (string) $mdadmContents, $match, \PREG_SET_ORDER)) {
            return null;
        }

        $mdadmArrays = [];
        foreach ($match as $array) {
            $drives = [];
            foreach (\explode(' ', $array['drives']) as $drive) {
                if (1 === \preg_match('/(?P<device>[\w\d]+)\[\d+\](?P<state>\(\w\))?/', $drive, $matchDrive)) {
                    // Determine a status other than normal, like if it failed or is a spare
                    if (isset($matchDrive['state'])) {
                        $driveState = match ($matchDrive['state']) {
                            '(S)' => 'spare',
                            '(F)' => 'failed',
                            default => 'normal',
                        };
                    } else {
                        $driveState = 'normal';
                    }

                    $drives[] = [
                        'path' => '/dev/'.$matchDrive['device'],
                        'state' => $driveState,
                    ];
                }
            }

            [$countTotal, $countActive] = \explode('/', $array['counts'], 2);

            $state = $array['state'];
            if (isset($array['blurb'])) {
                $state .= $array['blurb'];
            }

            $mdadmArrays[] = [
                'device' => '/dev/'.$array['device'],
                'status' => $state,
                'level' => $array['level'],
                'drives' => $drives,
                'size' => $array['blocks'] * 1024,
                'count' => [
                    'active' => (int) $countActive,
                    'total' => (int) $countTotal,
                ],
                'chart' => $array['chart'],
            ];
        }

        return $mdadmArrays;
    }
}
