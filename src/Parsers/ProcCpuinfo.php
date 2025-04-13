<?php

namespace Ginfo\Parsers;

use Ginfo\Common;

final readonly class ProcCpuinfo implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array{physical: int, cores: int, virtual: int, hyperThreading: bool, processors: array{model: string, speed: int, l2Cache: int|null, flags: string[]|null, architecture: string|null}[]}|null
     */
    public static function work(): ?array
    {
        $cpuInfo = Common::getContents('/proc/cpuinfo');
        if (null === $cpuInfo) {
            return null;
        }

        $cpuData = [];
        foreach (\explode("\n\n", $cpuInfo) as $block) {
            $cpuData[] = Common::parseKeyValueBlock($block);
        }

        $cores = (static function () use ($cpuData): int {
            $out = [];
            foreach ($cpuData as $block) {
                $out[$block['physical id']] = $block['cpu cores'];
            }

            return (int) \array_sum($out);
        })();
        $virtual = \count($cpuData);

        $physical = (static function () use ($cpuData): int {
            $out = [];
            foreach ($cpuData as $block) {
                if (isset($out[$block['physical id']])) {
                    ++$out[$block['physical id']];
                } else {
                    $out[$block['physical id']] = 1;
                }
            }

            return \count($out);
        })();

        $processors = [];
        foreach ($cpuData as $block) {
            if (isset($processors[$block['physical id']])) {
                continue;
            }
            $flags = \explode(' ', $block['flags']);
            $architecture = self::getArchitecture($block['model name'], $flags, $block['isa'] ?? null);

            $processors[$block['physical id']] = [
                'model' => $block['model name'],
                'speed' => $block['cpu MHz'],
                'l2Cache' => (float) $block['cache size'] * 1024, // L2 cache, drop KB
                'flags' => $flags,
                'architecture' => $architecture,
            ];
        }
        $processors = \array_values($processors);

        return [
            'physical' => $physical,
            'cores' => $cores,
            'virtual' => $virtual,
            'hyperThreading' => $cores < $virtual,
            'processors' => $processors,
        ];
    }

    /**
     * @todo more architectures
     *
     * @param string[] $flags
     */
    private static function getArchitecture(string $modelName, array $flags, ?string $isa): ?string
    {
        if ($isa && \str_contains($isa, 'rv64')) {
            return 'RISC-V';
        }

        $modelNameLower = \strtolower($modelName);
        if (\str_contains($modelNameLower, 'aarch64')) {
            return 'ARM64';
        }
        if (\str_contains($modelNameLower, 'arm')) {
            return 'ARM';
        }
        if (\str_contains($modelNameLower, 'loongson')) {
            return 'LoongArch';
        }
        if (\str_contains($modelNameLower, 'mips')) {
            return 'MIPS';
        }

        foreach ($flags as $flag) {
            if ('lm' === $flag || \str_ends_with($flag, '_lm')) { // lm, lahf_lm
                return 'x64';
            }
            if ('ia64' === $flag) {
                return 'ia64';
            }
        }

        return 'x86'; // default x86
    }
}
