<?php

namespace Ginfo;

use Ginfo\Os\Linux;
use Ginfo\Os\OsInterface;
use Ginfo\Os\Windows;

final readonly class Ginfo
{
    private OsInterface $os;

    public function __construct()
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            $this->os = new Windows();
        } else {
            $this->os = new Linux();
        }
    }

    public function getInfo(): Info
    {
        return new Info($this->os);
    }

    public function getOs(): OsInterface
    {
        return $this->os;
    }
}
