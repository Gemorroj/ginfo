<?php

namespace Ginfo\Info\Network;

final readonly class Stats
{
    public function __construct(
        private int $bytes,
        private int $errors,
        private int $packets,
    ) {
    }

    public function getBytes(): int
    {
        return $this->bytes;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function getPackets(): int
    {
        return $this->packets;
    }
}
