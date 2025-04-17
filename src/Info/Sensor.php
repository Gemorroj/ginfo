<?php

namespace Ginfo\Info;

final readonly class Sensor implements InfoInterface
{
    public function __construct(
        private string $name,
        private float $value,
        private ?string $unit = null,
        private ?string $path = null,
    ) {
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * C - celsius, F - Fahrenheit, V - Volt, W - Watt, RPM - revolution per minute, % - Percent.
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }
}
