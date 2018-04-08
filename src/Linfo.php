<?php

namespace Linfo;

use Linfo\Exceptions\FatalException;
use Linfo\OS\Linux;
use Linfo\OS\OS;
use Linfo\OS\Windows;

class Linfo
{
    /** @var OS */
    protected $os;

    /**
     * Linfo constructor.
     * @throws FatalException
     */
    public function __construct()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->os = new Windows();
        } else {
            // bsd, linux, darwin, solaris
            $this->os = new Linux();
        }
    }


    /**
     * @return Info
     */
    public function getInfo() : Info
    {
        return new Info($this->os);
    }


    /**
     * @return Linux|Windows
     */
    public function getOs() : OS
    {
        return $this->os;
    }
}
