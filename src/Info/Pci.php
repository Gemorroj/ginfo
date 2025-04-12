<?php

namespace Ginfo\Info;

final readonly class Pci
{
    public function __construct(private string $vendor, private ?string $name = null)
    {
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
