<?php

namespace Linfo\Parsers;

interface Parser
{
    /**
     * @return array|null
     */
    public static function work() : ?array;
}
