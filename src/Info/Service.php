<?php

namespace Ginfo\Info;

final readonly class Service implements InfoInterface
{
    public const TYPE_SERVICE = 'service';
    public const TYPE_TARGET = 'target';

    public function __construct(
        private string $name,
        private string $description,
        private bool $loaded,
        private bool $started,
        private string $state,
        private ?string $type = null
    ) {
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function getState(): string
    {
        return $this->state;
    }
}
