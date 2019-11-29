<?php

namespace Ginfo\Info;

class Sensor
{
    /** @var string|null */
    private $path;
    /** @var string */
    private $name;
    /** @var float */
    private $value;
    /** @var string|null */
    private $unit;

    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * C - celsius, F - Fahrenheit, V - Volt, W - Watt, RPM - revolution per minute, % - Percent.
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * @return $this
     */
    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
