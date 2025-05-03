<?php

namespace Ginfo\Info\Database;

final readonly class SqlitePragmaTable
{
    public function __construct(
        private string $schema,
        private string $name,
        private string $type,
        private int $ncol,
        private int $wr,
        private int $strict,
    ) {
    }

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNcol(): int
    {
        return $this->ncol;
    }

    public function getWr(): int
    {
        return $this->wr;
    }

    public function getStrict(): int
    {
        return $this->strict;
    }
}
