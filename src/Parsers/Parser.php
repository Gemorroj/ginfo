<?php

namespace Ginfo\Parsers;

interface Parser
{
    /**
     * @return array|null
     */
    public static function work() : ?array;
}
