<?php

namespace Ginfo\Parser;

use Ginfo\CommonTrait;

final readonly class ProcMeminfo implements ParserInterface
{
    use CommonTrait;

    /**
     * @return array{
     *     total: float,
     *     used: float,
     *     free: float,
     *     available: float,
     *     shared: float,
     *     buffers: float,
     *     cached: float,
     *     swapTotal: float,
     *     swapUsed: float,
     *     swapFree: float,
     * }|null
     */
    public function run(): ?array
    {
        // https://www.kernel.org/doc/html/latest/filesystems/proc.html#meminfo
        $contents = self::getContents('/proc/meminfo');
        if (null === $contents) {
            return null;
        }

        /** @var array<string, float> $arr */
        $arr = [];
        $lines = \explode("\n", $contents);
        foreach ($lines as $line) {
            [$key, $value] = \explode(':', $line, 2);
            $key = \trim($key);
            $value = \trim($value);
            $value = \trim(\explode(' ', $value, 2)[0]);
            $value = ((float) $value) * 1024; // unit always kB
            $arr[$key] = $value;
        }

        return [
            'total' => $arr['MemTotal'],
            'used' => $arr['MemTotal'] - $arr['MemFree'] - ($arr['Cached'] - $arr['Shmem'] + $arr['Buffers'] + $arr['KReclaimable']),
            'free' => $arr['MemFree'],
            'available' => $arr['MemAvailable'],
            'shared' => $arr['Shmem'],
            'buffers' => $arr['Buffers'],
            'cached' => $arr['Cached'] - $arr['Shmem'] + $arr['Buffers'] + $arr['KReclaimable'],
            'swapTotal' => $arr['SwapTotal'],
            'swapUsed' => $arr['SwapTotal'] - $arr['SwapFree'],
            'swapFree' => $arr['SwapFree'],
        ];
    }
}
