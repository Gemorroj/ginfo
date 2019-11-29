<?php

namespace Ginfo\Info;

class SoundCard
{
    /** @var string */
    private $vendor;
    /** @var string */
    private $name;

    public function getVendor(): string
    {
        return $this->vendor;
    }

    /**
     * @return $this
     */
    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;

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
}
