<?php

namespace Ginfo\Info\Network;

class Stats
{
    /** @var int */
    private $bytes;
    /** @var int */
    private $errors;
    /** @var int */
    private $packets;

    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @return $this
     */
    public function setBytes(int $bytes): self
    {
        $this->bytes = $bytes;

        return $this;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    /**
     * @return $this
     */
    public function setErrors(int $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getPackets(): int
    {
        return $this->packets;
    }

    /**
     * @return $this
     */
    public function setPackets(int $packets): self
    {
        $this->packets = $packets;

        return $this;
    }
}
