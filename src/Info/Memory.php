<?php

namespace Ginfo\Info;

final readonly class Memory implements InfoInterface
{
    public function __construct(
        private float $total,
        private float $used,
        private float $free,
        private float $available,
        private float $shared,
        private float $buffers,
        private float $cached,
        private float $swapTotal,
        private float $swapUsed,
        private float $swapFree,
    ) {
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getUsed(): float
    {
        return $this->used;
    }

    public function getFree(): float
    {
        return $this->free;
    }

    public function getAvailable(): float
    {
        return $this->available;
    }

    public function getShared(): float
    {
        return $this->shared;
    }

    public function getBuffers(): float
    {
        return $this->buffers;
    }

    public function getCached(): float
    {
        return $this->cached;
    }

    public function getSwapTotal(): float
    {
        return $this->swapTotal;
    }

    public function getSwapUsed(): float
    {
        return $this->swapUsed;
    }

    public function getSwapFree(): float
    {
        return $this->swapFree;
    }
}
