<?php

namespace Linfo\Info;

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

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return $this
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return float
     */
    public function getUsed(): float
    {
        return $this->used;
    }

    /**
     * @param float $used
     * @return $this
     */
    public function setUsed(float $used): self
    {
        $this->used = $used;
        return $this;
    }

    /**
     * @return float
     */
    public function getFree(): float
    {
        return $this->free;
    }

    /**
     * @param float $free
     * @return $this
     */
    public function setFree(float $free): self
    {
        $this->free = $free;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getShared(): ?float
    {
        return $this->shared;
    }

    /**
     * @param float|null $shared
     * @return $this
     */
    public function setShared(?float $shared): self
    {
        $this->shared = $shared;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBuffers(): ?float
    {
        return $this->buffers;
    }

    /**
     * @param float|null $buffers
     * @return $this
     */
    public function setBuffers(?float $buffers): self
    {
        $this->buffers = $buffers;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCached(): ?float
    {
        return $this->cached;
    }

    /**
     * @param float|null $cached
     * @return $this
     */
    public function setCached(?float $cached): self
    {
        $this->cached = $cached;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSwapTotal(): ?float
    {
        return $this->swapTotal;
    }

    /**
     * @param float|null $swapTotal
     * @return $this
     */
    public function setSwapTotal(?float $swapTotal): self
    {
        $this->swapTotal = $swapTotal;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSwapUsed(): ?float
    {
        return $this->swapUsed;
    }

    /**
     * @param float|null $swapUsed
     * @return $this
     */
    public function setSwapUsed(?float $swapUsed): self
    {
        $this->swapUsed = $swapUsed;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSwapFree(): ?float
    {
        return $this->swapFree;
    }

    /**
     * @param float|null $swapFree
     * @return $this
     */
    public function setSwapFree(?float $swapFree): self
    {
        $this->swapFree = $swapFree;
        return $this;
    }
}
