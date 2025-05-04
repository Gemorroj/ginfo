<?php

namespace Ginfo\Parser;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Who implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return string[]|null
     */
    public function run(?string $cwd = null, int $timeout = 1): ?array
    {
        $process = new Process(['who', '--count'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $list = $process->getOutput();
        $list = \explode("\n", \trim($list));
        \array_pop($list); // remove footer

        $out = [];
        foreach ($list as $line) {
            $out[] = \trim($line);
        }

        return $out;
    }
}
