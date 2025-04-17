<?php

namespace Ginfo\Info;

final readonly class SoundCard implements InfoInterface
{
    public function __construct(private string $vendor, private string $name)
    {
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
