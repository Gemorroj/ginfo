<?php

namespace Ginfo\Parser;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

/**
 * Get info on a cups install by running lpstat.
 */
final readonly class Lpstat implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{name: string, enabled: bool}[]|null
     */
    public function run(?string $cwd = null): ?array
    {
        $process = new Process(['lpstat', '-p'], $cwd, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
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
