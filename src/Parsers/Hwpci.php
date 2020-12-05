<?php

namespace Ginfo\Parsers;

use Ginfo\Common;

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
    private function __construct($file)
    {
        $this->file = $file;
    }

    private function __clone()
    {
    }

    /**
     * Get the USB ids from /sys.
     */
    private function fetchUsbIdsLinux(): void
    {
        foreach (\glob('/sys/bus/usb/devices/*', \GLOB_NOSORT) as $path) {
            // Avoid the same device artificially appearing more than once
            if (false !== \strpos($path, ':')) {
                continue;
            }

            // First try uevent
            if (\is_readable($path.'/uevent') &&
                \preg_match('/^product=([^\/]+)\/([^\/]+)\/[^$]+$/m', \mb_strtolower(Common::getContents($path.'/uevent')), $match)) {
                $vId = \str_pad($match[1], 4, '0', \STR_PAD_LEFT);
                $dId = \str_pad($match[2], 4, '0', \STR_PAD_LEFT);
                $this->entries[$vId][$dId] = 1;
            } // And next modalias
            elseif (\is_readable($path.'/modalias') &&
                \preg_match('/^usb:v([0-9A-Z]{4})p([0-9A-Z]{4})/', Common::getContents($path.'/modalias'), $match)) {
                $vId = \mb_strtolower($match[1]);
                $dId = \mb_strtolower($match[2]);
                $this->entries[$vId][$dId] = 1;
            }
        }
    }

    /**
     * Get the PCI ids from /sys.
     */
    private function fetchPciIdsLinux(): void
    {
        foreach (\glob('/sys/bus/pci/devices/*', \GLOB_NOSORT) as $path) {
            // See if we can use simple vendor/device files and avoid taking time with regex
            if (($fDevice = Common::getContents($path.'/device', '')) && ($fVend = Common::getContents($path.'/vendor', ''))) {
                [, $vId] = \explode('x', $fVend, 2);
                [, $dId] = \explode('x', $fDevice, 2);
                $this->entries[$vId][$dId] = 1;
            } // Try uevent nextly
            elseif (\is_readable($path.'/uevent') && \preg_match('/pci\_(?:subsys_)?id=(\w+):(\w+)/', \mb_strtolower(Common::getContents($path.'/uevent')), $match)) {
                $vId = $match[1];
                $dId = $match[2];
                $this->entries[$vId][$dId] = 1;
            } // Now for modalias
            elseif (\is_readable($path.'/modalias') && \preg_match('/^pci:v0{4}([0-9A-Z]{4})d0{4}([0-9A-Z]{4})/i', Common::getContents($path.'/modalias'), $match)) {
                $vId = \mb_strtolower($match[1]);
                $dId = \mb_strtolower($match[2]);
                $this->entries[$vId][$dId] = 1;
            }
        }
    }

    /**
     * Use the pci.ids file to translate the ids to names.
     */
    private function fetchPciNames(): void
    {
        for ($v = false, $file = \fopen($this->file, 'r'); false !== $file && $contents = \fgets($file);) {
            if (1 === \preg_match('/^(\S{4})\s+([^$]+)$/', $contents, $vendMatch)) {
                $v = $vendMatch;
            } elseif (1 === \preg_match('/^\s+(\S{4})\s+([^$]+)$/', $contents, $devMatch)) {
                if ($v) {
                    $vId = \mb_strtolower($v[1]);
                    $dId = \mb_strtolower($devMatch[1]);
                    if (isset($this->entries[$vId][$dId])) {
                        $this->devices[$vId][$dId] = [
                            'vendor' => \rtrim($v[2]),
                            'name' => \rtrim($devMatch[2]),
                        ];
                    }
                }
            }
        }
        $file && \fclose($file);
    }

    /**
     * Use the usb.ids file to translate the ids to names.
     */
    private function fetchUsbNames(): void
    {
        for ($v = false, $file = \fopen($this->file, 'r'); false !== $file && $contents = \fgets($file);) {
            if (1 === \preg_match('/^(\S{4})\s+([^$]+)$/', $contents, $vendMatch)) {
                $v = $vendMatch;
            } elseif (1 === \preg_match('/^\s+(\S{4})\s+([^$]+)$/', $contents, $devMatch)) {
                if ($v) {
                    $vId = \mb_strtolower($v[1]);
                    $dId = \mb_strtolower($devMatch[1]);
                    if (isset($this->entries[$vId][$dId])) {
                        $this->devices[$vId][$dId] = [
                            'vendor' => \rtrim($v[2]),
                            'name' => \rtrim($devMatch[2]),
                        ];
                    }
                }
            }
        }
        $file && \fclose($file);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function work(string $mode = self::MODE_PCI): ?array
    {
        if (self::MODE_PCI === $mode) {
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

        if (self::MODE_USB === $mode) {
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

        throw new \InvalidArgumentException('Unknown mode "'.$mode.'"');
    }

    /**
     * Compile and return results.
     */
    private function result(): array
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
