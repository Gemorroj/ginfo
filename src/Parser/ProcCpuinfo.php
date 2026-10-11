<?php

namespace Ginfo\Parser;

use Ginfo\CommonTrait;

final readonly class ProcCpuinfo implements ParserInterface
{
    use CommonTrait;

    /**
     * @return array{
     *     physical: int,
     *     cores: int,
     *     virtual: int,
     *     hyperThreading: bool,
     *     processors: array{
     *         model: string,
     *         speed: float,
     *         l2Cache: int|null,
     *         flags: string[],
     *         architecture: string
     *     }[]
     * }|null
     */
    public function run(): ?array
    {
        // https://docs.redhat.com/en/documentation/red_hat_enterprise_linux/6/html/deployment_guide/s2-proc-cpuinfo
        $cpuInfo = self::getContents('/proc/cpuinfo');
        if (null === $cpuInfo) {
            return null;
        }

        return self::parse($cpuInfo);
    }

    /**
     * @return array{
     *     physical: int,
     *     cores: int,
     *     virtual: int,
     *     hyperThreading: bool,
     *     processors: array{
     *         model: string,
     *         speed: float,
     *         l2Cache: int|null,
     *         flags: string[],
     *         architecture: string
     *     }[]
     * }
     */
    private static function parse(string $cpuInfo): array
    {
        $cpuData = [];
        foreach (\explode("\n\n", $cpuInfo) as $block) {
            $cpuData[] = self::parseKeyValueBlock($block);
        }

        $virtual = \count($cpuData);

        $cores = (static function () use ($cpuData): int {
            $out = [];
            $hasCores = false;
            foreach ($cpuData as $block) {
                if (isset($block['cpu cores'])) {
                    $hasCores = true;
                    $out[$block['physical id'] ?? '0'] = (int) $block['cpu cores'];
                }
            }

            // ARM and some other architectures don't expose "cpu cores"
            return $hasCores ? (int) \array_sum($out) : \count($cpuData);
        })();

        $physical = (static function () use ($cpuData): int {
            $out = [];
            foreach ($cpuData as $block) {
                $out[$block['physical id'] ?? '0'] = true;
            }

            return \count($out);
        })();

        $processors = [];
        foreach ($cpuData as $block) {
            $physicalId = $block['physical id'] ?? '0';
            if (isset($processors[$physicalId])) {
                continue;
            }

            $model = $block['model name'] ?? $block['Processor'] ?? $block['model'] ?? '';
            $flagsStr = $block['flags'] ?? $block['Features'] ?? '';
            $flags = '' === $flagsStr ? [] : \explode(' ', $flagsStr);
            $architecture = self::getArchitecture($model, $flags, $block['isa'] ?? null);

            $processors[$physicalId] = [
                'model' => $model,
                'speed' => (float) ($block['cpu MHz'] ?? 0),
                'l2Cache' => isset($block['cache size']) ? (int) ((float) $block['cache size'] * 1024) : null, // L2 cache, drop KB
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
    private static function getArchitecture(string $modelName, array $flags, ?string $isa): string
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
