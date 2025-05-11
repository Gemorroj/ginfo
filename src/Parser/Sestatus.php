<?php

namespace Ginfo\Parser;

use Ginfo\CommonTrait;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Sestatus implements ParserInterface
{
    use CommonTrait;

    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{enabled: bool, mode: string|null, policy: string|null}|null
     */
    public function run(?string $cwd = null, int $timeout = 1): ?array
    {
        $process = new Process(['sestatus'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $result = \trim($process->getOutput());
        $block = self::parseKeyValueBlock($result);

        return [
            'enabled' => 'enabled' === $block['SELinux status'],
            'mode' => $block['Current mode'] ?? null,
            'policy' => $block['Loaded policy name'] ?? null,
        ];
    }
}
