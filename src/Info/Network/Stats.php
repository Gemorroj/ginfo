<?php

namespace Ginfo\Info\Network;

final class Stats
{
    private int $bytes;
    private int $errors;
    private int $packets;

    public function getBytes(): int
    {
        return $this->bytes;
    }

    public function setBytes(int $bytes): self
    {
        $this->bytes = $bytes;

        return $this;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function setErrors(int $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getPackets(): int
    {
        return $this->packets;
    }

    public function setPackets(int $packets): self
    {
        $this->packets = $packets;

        return $this;
    }
}
