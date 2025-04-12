<?php

namespace Ginfo\Info;

final readonly class Usb
{
    public function __construct(private string $vendor, private ?string $name = null, private ?int $speed = null)
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

    public function getSpeed(): ?int
    {
        return $this->speed;
    }
}
