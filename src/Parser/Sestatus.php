<?php

namespace Ginfo\Parser;

use Ginfo\Common;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Sestatus implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{enabled: bool, mode: string|null, policy: string|null}|null
     */
    public static function work(): ?array
    {
        $process = new Process(['sestatus'], null, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
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
