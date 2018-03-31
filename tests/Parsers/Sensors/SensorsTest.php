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
namespace Linfo\Tests\Parsers\Sensors;

use Linfo\Parsers\Sensors\Sensors;

class SensorsTest extends \PHPUnit\Framework\TestCase
{
    public function sensorLineStrings()
    {
        return [
            ['acpitz-virtual-0', false],
            ['Adapter: Virtual device', false],
            ['                       (crit = +105.0°C, hyst =  +5.0°C)', false],
            ['Core0 Temp: +26.0°C', true],
            ['Vcore: +1.32 V (min = +0.00 V, max = +1.74 V)', true],
            ['cpu0_vid: +0.000 V', true],
            ['temp2: +63.5°C (high = +65.0°C, hyst = +65.0°C) sensor = diode', true],
            ['fan3: 0 RPM (min = 1506 RPM, div = 128) ALARM', true],
        ];
    }
    public function sensorStrings()
    {
        return [
            ['temp1: +60.0°C (crit = +90.0°C)', [
                'path' => null,
                'name' => 'temp1',
                'value' => '+60.0',
                'unit' => 'C',
            ]],
            ['Core 0:       +58.0°C  (high = +84.0°C, crit = +100.0°C)', [
                'path' => null,
                'name' => 'Core 0',
                'value' => '+58.0',
                'unit' => 'C',
            ]],
            ['Vcore: +1.32 V (min = +0.00 V, max = +1.74 V)', [
                'path' => null,
                'name' => 'Vcore',
                'value' => '+1.32',
                'unit' => 'V',
            ]],
            ['fan3: 0 RPM (min = 1506 RPM, div = 128) ALARM', [
                'path' => null,
                'name' => 'fan3',
                'value' => '0',
                'unit' => 'RPM',
            ]],
            ['cpu0_vid: +0.000 V', [
                'path' => null,
                'name' => 'cpu0_vid',
                'value' => '+0.000',
                'unit' => 'V',
            ]],
        ];
    }

    /**
     * @dataProvider sensorLineStrings
     *
     * @param string $data
     * @param bool $expected
     */
    public function testIsSensorLine(string $data, bool $expected)
    {
        $method = new \ReflectionMethod(Sensors::class, 'isSensorLine');
        $method->setAccessible(true);

        $actual = $method->invoke(null, $data);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider sensorStrings
     *
     * @param string $data
     * @param array $expected
     */
    public function testParseSensor(string $data, array $expected)
    {
        $method = new \ReflectionMethod(Sensors::class, 'parseSensor');
        $method->setAccessible(true);

        $actual = $method->invoke(null, $data);

        $this->assertEquals($expected, $actual);
    }
}