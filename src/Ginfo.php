<?php

namespace Ginfo;

use Ginfo\Exceptions\FatalException;
use Ginfo\OS\Linux;
use Ginfo\OS\OS;
use Ginfo\OS\Windows;

class Ginfo
{
    /** @var OS */
    protected $os;

    /**
     * @throws FatalException
     */
    public function __construct()
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
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
