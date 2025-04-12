<?php

namespace Ginfo\Info;

final readonly class Printer
{
    public function __construct(private string $name, private bool $enabled)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
