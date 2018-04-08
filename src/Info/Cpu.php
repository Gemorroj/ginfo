<?php

namespace Linfo\Info;

use Linfo\Info\Cpu\Processor;

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
     * @return $this
     */
    public function setProcessors(array $processors): self
    {
        $this->processors = $processors;
        return $this;
    }

    /**
     * @param Processor $processor
     * @return $this
     */
    public function addProcessor(Processor $processor): self
    {
        $this->processors[] = $processor;
        return $this;
    }

    /**
     * @return int
     */
    public function getPhysical(): int
    {
        return $this->physical;
    }

    /**
     * @param int $physical
     * @return $this
     */
    public function setPhysical(int $physical): self
    {
        $this->physical = $physical;
        return $this;
    }

    /**
     * @return int
     */
    public function getCores(): int
    {
        return $this->cores;
    }

    /**
     * @param int $cores
     * @return $this
     */
    public function setCores(int $cores): self
    {
        $this->cores = $cores;
        return $this;
    }

    /**
     * @return int
     */
    public function getVirtual(): int
    {
        return $this->virtual;
    }

    /**
     * @param int $virtual
     * @return $this
     */
    public function setVirtual(int $virtual): self
    {
        $this->virtual = $virtual;
        return $this;
    }


    /**
     * @return bool
     */
    public function getHyperThreading(): bool
    {
        return $this->hyperThreading;
    }

    /**
     * @param bool $hyperThreading
     * @return $this
     */
    public function setHyperThreading(bool $hyperThreading): self
    {
        $this->hyperThreading = $hyperThreading;
        return $this;
    }
}
