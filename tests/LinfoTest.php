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
namespace Linfo\Tests;

use Linfo\Linfo;
use Linfo\Meta\Errors;

if (class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

class LinfoTest extends \PHPUnit\Framework\TestCase
{
    /*
    public function testTodo()
    {
        $linfo = new Linfo();
        $linfo->scan();
        $info = $linfo->getInfo();
        print_r($info);

        //$errors = Errors::show();
        //print_r($errors);
        //$this->assertEmpty($errors);

        $this->assertInternalType('array', $info);
        //self::markTestSkipped('not implemented');
    }
    */


    public function testNew()
    {
        $linfo = new Linfo();
        $linfo->scan();
        $info = $linfo->getInfo();

        \print_r($info->getGeneral());
    }
}
