<?php

namespace Ginfo\Parsers;

use Ginfo\Info\Service;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Systemd implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{name: string, loaded: bool, started: bool, state: string, description: string}[]|null
     */
    public static function work(string $type = Service::TYPE_SERVICE): ?array
    {
        return match ($type) {
            Service::TYPE_SERVICE => self::services(),
            Service::TYPE_TARGET => self::targets(),
            default => null,
        };
    }

    private static function targets(): ?array
    {
        $process = new Process(['systemctl', 'list-units', '--type', 'target', '--all'], null, ['LANG' => 'C']);
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

    private static function services(): ?array
    {
        $process = new Process(['systemctl', 'list-units', '--type', 'service', '--all'], null, ['LANG' => 'C']);
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
