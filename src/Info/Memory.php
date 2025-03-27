<?php

namespace Ginfo\Info;

final class Memory
{
    private float $total;
    private float $used;
    private float $free;
    private ?float $shared = null;
    private ?float $buffers = null;
    private ?float $cached = null;

    private ?float $swapTotal = null;
    private ?float $swapUsed = null;
    private ?float $swapFree = null;

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUsed(): float
    {
        return $this->used;
    }

    public function setUsed(float $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getFree(): float
    {
        return $this->free;
    }

    public function setFree(float $free): self
    {
        $this->free = $free;

        return $this;
    }

    public function getShared(): ?float
    {
        return $this->shared;
    }

    public function setShared(?float $shared): self
    {
        $this->shared = $shared;

        return $this;
    }

    public function getBuffers(): ?float
    {
        return $this->buffers;
    }

    public function setBuffers(?float $buffers): self
    {
        $this->buffers = $buffers;

        return $this;
    }

    public function getCached(): ?float
    {
        return $this->cached;
    }

    public function setCached(?float $cached): self
    {
        $this->cached = $cached;

        return $this;
    }

    public function getSwapTotal(): ?float
    {
        return $this->swapTotal;
    }

    public function setSwapTotal(?float $swapTotal): self
    {
        $this->swapTotal = $swapTotal;

        return $this;
    }

    public function getSwapUsed(): ?float
    {
        return $this->swapUsed;
    }

    public function setSwapUsed(?float $swapUsed): self
    {
        $this->swapUsed = $swapUsed;

        return $this;
    }

    public function getSwapFree(): ?float
    {
        return $this->swapFree;
    }

    public function setSwapFree(?float $swapFree): self
    {
        $this->swapFree = $swapFree;

        return $this;
    }
}
