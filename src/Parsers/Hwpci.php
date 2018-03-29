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
class Hwpci implements Parser
{
    const MODE_PCI = 'pci';
    const MODE_USB = 'usb';

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
    private function fetchUsbIdsLinux() : void
    {
        foreach (\glob('/sys/bus/usb/devices/*', \GLOB_NOSORT) as $path) {
            // First try uevent
            if (\is_readable($path . '/uevent') &&
                \preg_match('/^product=([^\/]+)\/([^\/]+)\/[^$]+$/m', \mb_strtolower(Common::getContents($path . '/uevent')), $match)) {
                $this->entries[\str_pad($match[1], 4, '0', \STR_PAD_LEFT)][\str_pad($match[2], 4, '0', \STR_PAD_LEFT)] = 1;
            } // And next modalias 
            elseif (\is_readable($path . '/modalias') &&
                \preg_match('/^usb:v([0-9A-Z]{4})p([0-9A-Z]{4})/', Common::getContents($path . '/modalias'), $match)) {
                $this->entries[\mb_strtolower($match[1])][\mb_strtolower($match[2])] = 1;
            }
        }
    }

    /**
     * Get the PCI ids from /sys.
     */
    private function fetchPciIdsLinux() : void
    {
        foreach (\glob('/sys/bus/pci/devices/*', \GLOB_NOSORT) as $path) {
            // See if we can use simple vendor/device files and avoid taking time with regex
            if (($fDevice = Common::getContents($path . '/device', '')) && ($fVend = Common::getContents($path . '/vendor', '')) && $fDevice && $fVend) {
                list(, $vId) = \explode('x', $fVend, 2);
                list(, $dId) = \explode('x', $fDevice, 2);
                $this->entries[$vId][$dId] = 1;
            } // Try uevent nextly
            elseif (\is_readable($path . '/uevent') && \preg_match('/pci\_(?:subsys_)?id=(\w+):(\w+)/', \mb_strtolower(Common::getContents($path . '/uevent')), $match)) {
                $this->entries[$match[1]][$match[2]] = 1;
            } // Now for modalias
            elseif (\is_readable($path . '/modalias') && \preg_match('/^pci:v0{4}([0-9A-Z]{4})d0{4}([0-9A-Z]{4})/', Common::getContents($path . '/modalias'), $match)) {
                $this->entries[\mb_strtolower($match[1])][\mb_strtolower($match[2])] = 1;
            }
        }
    }

    /**
     * Use the pci.ids file to translate the ids to names.
     */
    private function fetchPciNames() : void
    {
        for ($v = false, $file = \fopen($this->file, 'r'); $file !== false && $contents = \fgets($file);) {
            if (\preg_match('/^(\S{4})\s+([^$]+)$/', $contents, $vendMatch) === 1) {
                $v = $vendMatch;
            } elseif (\preg_match('/^\s+(\S{4})\s+([^$]+)$/', $contents, $devMatch) === 1) {
                if ($v && isset($this->entries[\mb_strtolower($v[1])][\mb_strtolower($devMatch[1])])) {
                    $this->devices[$v[1]][$devMatch[1]] = ['vendor' => \rtrim($v[2]), 'name' => \rtrim($devMatch[2])];
                }
            }
        }
        $file && \fclose($file);
    }

    /**
     * Use the usb.ids file to translate the ids to names.
     */
    private function fetchUsbNames() : void
    {
        for ($v = false, $file = \fopen($this->file, 'r'); $file !== false && $contents = \fgets($file);) {
            if (\preg_match('/^(\S{4})\s+([^$]+)$/', $contents, $vendMatch) === 1) {
                $v = $vendMatch;
            } elseif (\preg_match('/^\s+(\S{4})\s+([^$]+)$/', $contents, $devMatch) === 1) {
                if ($v && isset($this->entries[\mb_strtolower($v[1])][\mb_strtolower($devMatch[1])])) {
                    $this->devices[\mb_strtolower($v[1])][$devMatch[1]] = ['vendor' => \rtrim($v[2]), 'name' => \rtrim($devMatch[2])];
                }
            }
        }
        $file && \fclose($file);
    }

    /**
     * @param string $mode
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public static function work(string $mode = Hwpci::MODE_PCI) : ?array
    {
        if (Hwpci::MODE_PCI === $mode) {
            $pciIds = Common::locateActualPath([
                '/usr/share/misc/pci.ids',    // debian/ubuntu
                '/usr/share/pci.ids',        // opensuse
                '/usr/share/hwdata/pci.ids',    // centos. maybe also redhat/fedora
            ]);

            if (!$pciIds) {
                return null;
            }

            $obj = new self($pciIds);

            $obj->fetchPciIdsLinux();
            $obj->fetchPciNames();

            return $obj->result();
        }

        if (Hwpci::MODE_USB === $mode) {
            $usbIds = Common::locateActualPath([
                '/usr/share/misc/usb.ids',    // debian/ubuntu
                '/usr/share/usb.ids',        // opensuse
                '/usr/share/hwdata/usb.ids',    // centos. maybe also redhat/fedora
            ]);

            if (!$usbIds) {
                return null;
            }

            $obj = new self($usbIds);
            $obj->fetchUsbIdsLinux();
            $obj->fetchUsbNames();

            return $obj->result();
        }

        throw new \InvalidArgumentException('Unknown mode "' . $mode . '"');
    }

    /**
     * Compile and return results.
     *
     * @return array
     */
    private function result() : array
    {
        $result = [];

        foreach (\array_keys($this->devices) as $v) {
            foreach ($this->devices[$v] as $d) {
                $result[] = [
                    'vendor' => $d['vendor'],
                    'name' => $d['name'],
                ];
            }
        }

        return $result;
    }
}
