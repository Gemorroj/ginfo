<?php

namespace Ginfo\Parser;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Who implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return string[]|null
     */
    public static function work(): ?array
    {
        $process = new Process(['who', '--count'], null, ['LANG' => 'C']);
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
