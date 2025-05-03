<?php

namespace Ginfo\Info\Database;

final readonly class MongoServerStatusNetwork
{
    public function __construct(
        private int|float $bytesIn,
        private int|float $bytesOut,
        private int|float $numRequests,
    ) {
    }

    public function getBytesIn(): float|int
    {
        return $this->bytesIn;
    }

    public function getBytesOut(): float|int
    {
        return $this->bytesOut;
    }

    public function getNumRequests(): float|int
    {
        return $this->numRequests;
    }
}
