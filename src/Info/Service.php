<?php

namespace Ginfo\Info;

class Service
{
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var bool */
    private $loaded;
    /** @var bool */
    private $started;
    /** @var string */
    private $state;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @return $this
     */
    public function setLoaded(bool $loaded): self
    {
        $this->loaded = $loaded;

        return $this;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @return $this
     */
    public function setStarted(bool $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
