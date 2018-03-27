<?php

/**
 * This file is part of Linfo (c) 2010 Joseph Gillotti.
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

namespace Linfo\Parsers;

use Linfo\Common;

/**
 * Deal with pci.ids and usb.ids workings.
 *
 * @author Joe Gillotti
 */
class Hwpci
{
    private $file;
    private $entries = [];
    private $devices = [];

    /**
     * @param string $file
     */
    final private function __construct($file)
    {
        $this->file = $file;
    }

    final private function __clone()
    {
    }

    /**
     * Get the USB ids from /sys.
     */
    public function fetchUsbIdsLinux()
    {
        foreach ((array)@\glob('/sys/bus/usb/devices/*', \GLOB_NOSORT) as $path) {

            // First try uevent
            if (\is_readable($path . '/uevent') &&
                \preg_match('/^product=([^\/]+)\/([^\/]+)\/[^$]+$/m', \strtolower(Common::getContents($path . '/uevent')), $match)) {
                $this->entries[\str_pad($match[1], 4, '0', \STR_PAD_LEFT)][\str_pad($match[2], 4, '0', \STR_PAD_LEFT)] = 1;
            } // And next modalias 
            elseif (\is_readable($path . '/modalias') &&
                \preg_match('/^usb:v([0-9A-Z]{4})p([0-9A-Z]{4})/', Common::getContents($path . '/modalias'), $match)) {
                $this->entries[\strtolower($match[1])][\strtolower($match[2])] = 1;
            }
        }
    }

    /**
     * Get the PCI ids from /sys.
     */
    public function fetchPciIdsLinux()
    {
        foreach ((array)@\glob('/sys/bus/pci/devices/*', \GLOB_NOSORT) as $path) {

            // See if we can use simple vendor/device files and avoid taking time with regex
            if (($f_device = Common::getContents($path . '/device', '')) && ($f_vend = Common::getContents($path . '/vendor', '')) && $f_device && $f_vend) {
                list(, $v_id) = \explode('x', $f_vend, 2);
                list(, $d_id) = \explode('x', $f_device, 2);
                $this->entries[$v_id][$d_id] = 1;
            } // Try uevent nextly
            elseif (\is_readable($path . '/uevent') && \preg_match('/pci\_(?:subsys_)?id=(\w+):(\w+)/', \strtolower(Common::getContents($path . '/uevent')), $match)) {
                $this->entries[$match[1]][$match[2]] = 1;
            } // Now for modalias
            elseif (\is_readable($path . '/modalias') && \preg_match('/^pci:v0{4}([0-9A-Z]{4})d0{4}([0-9A-Z]{4})/', Common::getContents($path . '/modalias'), $match)) {
                $this->entries[\strtolower($match[1])][\strtolower($match[2])] = 1;
            }
        }
    }

    /**
     * Use the pci.ids file to translate the ids to names.
     */
    public function fetchPciNames()
    {
        for ($v = false, $file = @\fopen($this->file, 'r'); $file !== false && $contents = \fgets($file);) {
            if (\preg_match('/^(\S{4})\s+([^$]+)$/', $contents, $vend_match) === 1) {
                $v = $vend_match;
            } elseif (\preg_match('/^\s+(\S{4})\s+([^$]+)$/', $contents, $dev_match) === 1) {
                if ($v && isset($this->entries[\strtolower($v[1])][\strtolower($dev_match[1])])) {
                    $this->devices[$v[1]][$dev_match[1]] = ['vendor' => \rtrim($v[2]), 'device' => \rtrim($dev_match[2])];
                }
            }
        }
        $file && @\fclose($file);
    }

    /**
     * Use the usb.ids file to translate the ids to names.
     */
    public function fetchUsbNames()
    {
        for ($v = false, $file = @\fopen($this->file, 'r'); $file !== false && $contents = \fgets($file);) {
            if (\preg_match('/^(\S{4})\s+([^$]+)$/', $contents, $vend_match) === 1) {
                $v = $vend_match;
            } elseif (\preg_match('/^\s+(\S{4})\s+([^$]+)$/', $contents, $dev_match) === 1) {
                if ($v && isset($this->entries[\strtolower($v[1])][\strtolower($dev_match[1])])) {
                    $this->devices[\strtolower($v[1])][$dev_match[1]] = ['vendor' => \rtrim($v[2]), 'device' => \rtrim($dev_match[2])];
                }
            }
        }
        $file && @fclose($file);
    }

    /**
     * @param string $file
     * @return array
     */
    public static function workUsb($file)
    {
        $obj = new self($file);
        $obj->fetchUsbIdsLinux();
        $obj->fetchUsbNames();

        return $obj->result();
    }

    /**
     * @param string $file
     * @return array
     */
    public static function workPci($file)
    {
        $obj = new self($file);

        $obj->fetchPciIdsLinux();
        $obj->fetchPciNames();

        return $obj->result();
    }

    /**
     * Compile and return results.
     *
     * @return array
     */
    public function result()
    {
        $result = [];

        foreach (\array_keys($this->devices) as $v) {
            foreach ($this->devices[$v] as $d) {
                $result[] = ['vendor' => $d['vendor'], 'device' => $d['device']];
            }
        }

        return $result;
    }
}
