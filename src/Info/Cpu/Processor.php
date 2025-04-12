<?php

namespace Ginfo\Info\Cpu;

final readonly class Processor
{
    public function __construct(
        private string $model,
        private float $speed,
        private ?int $l2Cache = null,
        /** @var string[]|null */
        private ?array $flags = null,
        private ?string $architecture = null,
    ) {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    public function getL2Cache(): ?int
    {
        return $this->l2Cache;
    }

    /**
     * @see https://unix.stackexchange.com/questions/43539/what-do-the-flags-in-proc-cpuinfo-mean#answer-43540
     *
     * @return string[]|null
     */
    public function getFlags(): ?array
    {
        return $this->flags;
    }

    /**
     * @return string|null (x86|x64|ia64) currently arm or mips not supported
     */
    public function getArchitecture(): ?string
    {
        return $this->architecture;
    }
}
