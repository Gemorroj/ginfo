<?php

/**
 * This file is part of Linfo (c) 2014, 2015 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Linfo;

use Linfo\Meta\Response;
use Linfo\OS\Linux;
use Linfo\OS\OS;
use Linfo\Exceptions\FatalException;
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
     * @return Response
     */
    public function getInfo()
    {
        return new Response($this->os);
    }


    /**
     * @return Linux|Windows
     */
    public function getOs()
    {
        return $this->os;
    }
}
