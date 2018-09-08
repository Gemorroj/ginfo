<?php

namespace Ginfo\Parsers;

use Symfony\Component\Process\Process;

class Systemd implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function work(): ?array
    {
        $process = new Process(['systemctl', 'list-units', '--type', 'service', '--all'], null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $list = $process->getOutput();

        $lines = \explode("\n", \explode("\n\n", $list, 2)[0]);
        \array_shift($lines); //remove header

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
