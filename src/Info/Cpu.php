<?php

namespace Ginfo\Info;

use Ginfo\Info\Cpu\Processor;

final class Cpu
{
    /** @var Processor[] */
    private array $processors = [];
    private int $physical;
    private int $cores;
    private int $virtual;
    private bool $hyperThreading;

    /**
     * @return Processor[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * @param Processor[] $processors
     */
    public function setProcessors(array $processors): self
    {
        $this->processors = $processors;

        return $this;
    }

    public function addProcessor(Processor $processor): self
    {
        $this->processors[] = $processor;

        return $this;
    }

    public function getPhysical(): int
    {
        return $this->physical;
    }

    public function setPhysical(int $physical): self
    {
        $this->physical = $physical;

        return $this;
    }

    public function getCores(): int
    {
        return $this->cores;
    }

    public function setCores(int $cores): self
    {
        $this->cores = $cores;

        return $this;
    }

    public function getVirtual(): int
    {
        return $this->virtual;
    }

    public function setVirtual(int $virtual): self
    {
        $this->virtual = $virtual;

        return $this;
    }

    public function isHyperThreading(): bool
    {
        return $this->hyperThreading;
    }

    public function setHyperThreading(bool $hyperThreading): self
    {
        $this->hyperThreading = $hyperThreading;

        return $this;
    }
}
