<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Mongo implements InfoInterface
{
    /**
     * @param array<string, MongoDatabase> $databases
     */
    public function __construct(
        private MongoServerStatus $serverStatus,
        private array $databases,
    ) {
    }

    public function getServerStatus(): MongoServerStatus
    {
        return $this->serverStatus;
    }

    /**
     * @return array<string, MongoDatabase>
     */
    public function getDatabases(): array
    {
        return $this->databases;
    }
}
