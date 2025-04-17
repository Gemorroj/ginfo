<?php

namespace Ginfo\Info;

use Ginfo\Info\Cpu\Processor;

final readonly class Cpu implements InfoInterface
{
    public function __construct(
        private int $physical,
        private int $cores,
        private int $virtual,
        private bool $hyperThreading,
        /** @var Processor[] */
        private array $processors = [],
    ) {
    }

    /**
     * @return Processor[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    public function getPhysical(): int
    {
        return $this->physical;
    }

    public function getCores(): int
    {
        return $this->cores;
    }

    public function getVirtual(): int
    {
        return $this->virtual;
    }

    public function isHyperThreading(): bool
    {
        return $this->hyperThreading;
    }
}
