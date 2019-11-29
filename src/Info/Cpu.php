<?php

namespace Ginfo\Info;

use Ginfo\Info\Cpu\Processor;

class Cpu
{
    /** @var Processor[] */
    private $processors;
    /** @var int */
    private $physical;
    /** @var int */
    private $cores;
    /** @var int */
    private $virtual;
    /** @var bool */
    private $hyperThreading;

    /**
     * @return Processor[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * @param Processor[] $processors
     *
     * @return $this
     */
    public function setProcessors(array $processors): self
    {
        $this->processors = $processors;

        return $this;
    }

    /**
     * @return $this
     */
    public function addProcessor(Processor $processor): self
    {
        $this->processors[] = $processor;

        return $this;
    }

    public function getPhysical(): int
    {
        return $this->physical;
    }

    /**
     * @return $this
     */
    public function setPhysical(int $physical): self
    {
        $this->physical = $physical;

        return $this;
    }

    public function getCores(): int
    {
        return $this->cores;
    }

    /**
     * @return $this
     */
    public function setCores(int $cores): self
    {
        $this->cores = $cores;

        return $this;
    }

    public function getVirtual(): int
    {
        return $this->virtual;
    }

    /**
     * @return $this
     */
    public function setVirtual(int $virtual): self
    {
        $this->virtual = $virtual;

        return $this;
    }

    public function isHyperThreading(): bool
    {
        return $this->hyperThreading;
    }

    /**
     * @return $this
     */
    public function setHyperThreading(bool $hyperThreading): self
    {
        $this->hyperThreading = $hyperThreading;

        return $this;
    }
}
