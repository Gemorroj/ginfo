<?php

namespace Ginfo\Parser;

use Ginfo\Common;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Sestatus implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{enabled: bool, mode: string|null, policy: string|null}|null
     */
    public function run(?string $cwd = null): ?array
    {
        $process = new Process(['sestatus'], $cwd, ['LANG' => 'C']);
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
