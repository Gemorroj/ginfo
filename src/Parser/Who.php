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
        $process = new Process(['who'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        return self::parse($process->getOutput());
    }

    /**
     * @return string[]
     */
    private static function parse(string $output): array
    {
        $out = [];
        foreach (\explode("\n", \trim($output)) as $line) {
            $line = \trim($line);
            if ('' === $line || \str_starts_with($line, 'USER') || \str_starts_with($line, 'system boot')) {
                continue; // skip header (busybox) and footer (GNU)
            }

            $user = \explode(' ', $line, 2)[0];
            if ('' !== $user) {
                $out[] = $user;
            }
        }

        return $out;
    }
}
