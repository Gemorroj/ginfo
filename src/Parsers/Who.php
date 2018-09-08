<?php

namespace Ginfo\Parsers;

use Symfony\Component\Process\Process;

class Who implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function work(): ?array
    {
        $process = new Process(['who', '--count'], null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
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
