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

    /**
     * @return int
     */
    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @param int $bytes
     * @return $this
     */
    public function setBytes(int $bytes): self
    {
        $this->bytes = $bytes;
        return $this;
    }

    /**
     * @return int
     */
    public function getErrors(): int
    {
        return $this->errors;
    }

    /**
     * @param int $errors
     * @return $this
     */
    public function setErrors(int $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return int
     */
    public function getPackets(): int
    {
        return $this->packets;
    }

    /**
     * @param int $packets
     * @return $this
     */
    public function setPackets(int $packets): self
    {
        $this->packets = $packets;
        return $this;
    }

}
