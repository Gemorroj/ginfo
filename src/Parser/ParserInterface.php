<?php

namespace Ginfo\Parser;

interface ParserInterface
{
    /**
     * @return array<mixed>|null
     */
    public function run(): ?array;
}
