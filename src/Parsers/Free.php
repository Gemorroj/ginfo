<?php

namespace Ginfo\Parsers;

use Symfony\Component\Process\Process;

class Free implements Parser
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function work(): ?array
    {
        $process = new Process(['free', '-bw'], null, ['LANG' => 'C']);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $free = $process->getOutput();

        $arr = \explode("\n", \trim($free));
        $isWideOutput = false === \strpos($arr[0], 'buff/cache'); // alpine doesnt support wide output for example
        \array_shift($arr); // remove header

        $memStr = \trim(\explode(':', $arr[0], 2)[1]);
        $swapStr = \trim(\explode(':', $arr[1], 2)[1]);

        if ($isWideOutput) {
            [$memTotal, $memUsed, $memFree, $memShared, $memBuffers, $memCached, $memAvailable] = \preg_split('/\s+/', $memStr);
        } else {
            $memBuffers = $memCached = null;
            [$memTotal, $memUsed, $memFree, $memShared, $memBuffCached, $memAvailable] = \preg_split('/\s+/', $memStr);
        }
        [$swapTotal, $swapUsed, $swapFree] = \preg_split('/\s+/', $swapStr);

        return [
            'total' => $memTotal,
            'used' => $memUsed,
            'free' => $memFree,
            'shared' => $memShared,
            'buffers' => $memBuffers,
            'cached' => $memCached,
            'available' => $memAvailable,

            'swapTotal' => $swapTotal,
            'swapUsed' => $swapUsed,
            'swapFree' => $swapFree,
        ];
    }
}
