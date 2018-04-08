<?php

namespace Linfo\Parsers;

use Linfo\Common;
use Symfony\Component\Process\Process;

class Sestatus implements Parser
{
    final private function __construct()
    {
    }

    final private function __clone()
    {
    }

    public static function work() : ?array
    {
        $process = new Process('sestatus', null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = \trim($process->getOutput());
        $block = Common::parseKeyValueBlock($result);

        return [
            'enabled' => 'enabled' === $block['SELinux status'],
            'mode' => $block['Current mode'] ?? null,
            'policy' => $block['Loaded policy name'] ?? null,
        ];
    }
}
