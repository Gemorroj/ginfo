<?php

namespace Ginfo;

use Ginfo\Info\InfoInterface;

interface InfoParserInterface
{
    public function run(): ?InfoInterface;
}
