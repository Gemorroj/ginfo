<?php

namespace Ginfo\Info;

class Pci
{
    /** @var string */
    private $vendor;
    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function getVendor(): string
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     * @return $this
     */
    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
