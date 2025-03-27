<?php

namespace Ginfo\Info\Cpu;

final class Processor
{
    private string $model;
    private float $speed;
    private ?int $l2Cache = null;
    /** @var string[]|null */
    private ?array $flags = null;
    private ?string $architecture = null;

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getL2Cache(): ?int
    {
        return $this->l2Cache;
    }

    public function setL2Cache(?int $l2Cache): self
    {
        $this->l2Cache = $l2Cache;

        return $this;
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
     * @param string[]|null $flags
     */
    public function setFlags(?array $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * @param string|null $architecture (x86|x64|ia64) currently arm or mips not supported
     */
    public function setArchitecture(?string $architecture): self
    {
        $this->architecture = $architecture;

        return $this;
    }

    /**
     * @return string|null (x86|x64|ia64) currently arm or mips not supported
     */
    public function getArchitecture(): ?string
    {
        return $this->architecture;
    }
}
