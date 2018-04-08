<?php

namespace Linfo\Info;

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @param bool $loaded
     * @return $this
     */
    public function setLoaded(bool $loaded): self
    {
        $this->loaded = $loaded;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @param bool $started
     * @return $this
     */
    public function setStarted(bool $started): self
    {
        $this->started = $started;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

}
