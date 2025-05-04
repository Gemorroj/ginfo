<?php

namespace Ginfo\Parser;

use Ginfo\Info\Service;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Systemd implements ParserInterface
{
    /**
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     *
     * @return array{name: string, loaded: bool, started: bool, state: string, description: string}[]|null
     */
    public function run(string $type = Service::TYPE_SERVICE, ?string $cwd = null, int $timeout = 1): ?array
    {
        return match ($type) {
            Service::TYPE_SERVICE => self::services($cwd, $timeout),
            Service::TYPE_TARGET => self::targets($cwd, $timeout),
            default => null,
        };
    }

    private static function targets(?string $cwd, int $timeout): ?array
    {
        $process = new Process(['systemctl', 'list-units', '--type', 'target', '--all'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $list = $process->getOutput();

        $lines = \explode("\n", \explode("\n\n", $list, 2)[0]);
        \array_shift($lines); // remove header

        $out = [];
        foreach ($lines as $line) {
            $line = \ltrim($line, '●');
            $line = \trim($line);
            [$unit, $load, $active, $sub, $description] = \preg_split('/\s+/', $line, 5);

            $out[] = [
                'name' => $unit,
                'loaded' => 'loaded' === $load,
                'started' => 'active' === $active,
                'state' => $sub,
                'description' => $description,
            ];
        }

        return $out;
    }

    private static function services(?string $cwd, int $timeout): ?array
    {
        $process = new Process(['systemctl', 'list-units', '--type', 'service', '--all'], $cwd, ['LANG' => 'C'], null, (float) $timeout);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $list = $process->getOutput();

        $lines = \explode("\n", \explode("\n\n", $list, 2)[0]);
        \array_shift($lines); // remove header

        $out = [];
        foreach ($lines as $line) {
            $line = \ltrim($line, '●');
            $line = \trim($line);
            [$unit, $load, $active, $sub, $description] = \preg_split('/\s+/', $line, 5);

            $out[] = [
                'name' => $unit,
                'loaded' => 'loaded' === $load,
                'started' => 'active' === $active,
                'state' => $sub,
                'description' => $description,
            ];
        }

        return $out;
    }
}
