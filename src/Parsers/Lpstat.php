<?php

namespace Ginfo\Parsers;

use Symfony\Component\Process\Process;

/**
 * Get info on a cups install by running lpstat.
 */
class Lpstat implements Parser
{
    public static function work(): ?array
    {
        $process = new Process(['lpstat', '-p'], null, ['LANG' => 'C']);
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }

        $lines = \explode("\n", \trim($process->getOutput()));

        $res = [];
        foreach ($lines as $line) {
            $line = \trim($line);

            if (\preg_match('/^printer (\w+) .*([enabled|disabled]+) since .+?/Uu', $line, $printersMatch)) {
                $res[] = [
                    'name' => \str_replace('_', ' ', $printersMatch[1]),
                    'enabled' => 'enabled' === $printersMatch[2],
                ];
            }
        }

        return $res;
    }
}
