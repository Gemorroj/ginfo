<?php

namespace Ginfo\Info;

class Memory
{
    /** @var float */
    private $total;
    /** @var float */
    private $used;
    /** @var float */
    private $free;
    /** @var float|null */
    private $shared;
    /** @var float|null */
    private $buffers;
    /** @var float|null */
    private $cached;

    /** @var float|null */
    private $swapTotal;
    /** @var float|null */
    private $swapUsed;
    /** @var float|null */
    private $swapFree;

    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @return $this
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUsed(): float
    {
        return $this->used;
    }

    /**
     * @return $this
     */
    public function setUsed(float $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getFree(): float
    {
        return $this->free;
    }

    /**
     * @return $this
     */
    public function setFree(float $free): self
    {
        $this->free = $free;

        return $this;
    }

    public function getShared(): ?float
    {
        return $this->shared;
    }

    /**
     * @return $this
     */
    public function setShared(?float $shared): self
    {
        $this->shared = $shared;

        return $this;
    }

    public function getBuffers(): ?float
    {
        return $this->buffers;
    }

    /**
     * @return $this
     */
    public function setBuffers(?float $buffers): self
    {
        $this->buffers = $buffers;

        return $this;
    }

    public function getCached(): ?float
    {
        return $this->cached;
    }

    /**
     * @return $this
     */
    public function setCached(?float $cached): self
    {
        $this->cached = $cached;

        return $this;
    }

    public function getSwapTotal(): ?float
    {
        return $this->swapTotal;
    }

    /**
     * @return $this
     */
    public function setSwapTotal(?float $swapTotal): self
    {
        $this->swapTotal = $swapTotal;

        return $this;
    }

    public function getSwapUsed(): ?float
    {
        return $this->swapUsed;
    }

    /**
     * @return $this
     */
    public function setSwapUsed(?float $swapUsed): self
    {
        $this->swapUsed = $swapUsed;

        return $this;
    }

    public function getSwapFree(): ?float
    {
        return $this->swapFree;
    }

    /**
     * @return $this
     */
    public function setSwapFree(?float $swapFree): self
    {
        $this->swapFree = $swapFree;

        return $this;
    }
}
