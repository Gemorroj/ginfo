<?php

namespace Ginfo;

use Ginfo\OS\Linux;
use Ginfo\OS\OS;
use Ginfo\OS\Windows;

final readonly class Ginfo
{
    private OS $os;

    public function __construct()
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            $this->os = new Windows();
        } else {
            // bsd, linux, darwin, solaris
            $this->os = new Linux();
        }
    }

    public function getInfo(): Info
    {
        return new Info($this->os);
    }

    /**
     * @return Linux|Windows
     */
    public function getOs(): OS
    {
        return $this->os;
    }
}
