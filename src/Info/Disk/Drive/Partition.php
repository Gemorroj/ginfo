<?php

namespace Ginfo\Info\Disk\Drive;

class Partition
{
    /** @var float */
    private $size;
    /** @var string */
    private $name;

    /**
     * @return float
     */
    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @param float $size
     *
     * @return $this
     */
    public function setSize(float $size): self
    {
        $this->size = $size;

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
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
