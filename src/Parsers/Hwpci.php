<?php

namespace Ginfo\Parsers;

use Ginfo\Common;

/**
 * Deal with pci.ids and usb.ids workings.
 *
 * @author Joe Gillotti
 */
final class Hwpci implements ParserInterface
{
    public const MODE_PCI = 'pci';
    public const MODE_USB = 'usb';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function work(string $mode = self::MODE_PCI): ?array
    {
        if (self::MODE_PCI === $mode) {
            $pciIds = Common::locateActualPath([
                '/usr/share/misc/pci.ids',   // debian/ubuntu
                '/usr/share/pci.ids',        // opensuse
                '/usr/share/hwdata/pci.ids', // centos. maybe also redhat/fedora
            ]);

            if (!$pciIds) {
                return null;
            }

            $obj = new self();

            return $obj->result($mode, $pciIds);
        }

        if (self::MODE_USB === $mode) {
            $usbIds = Common::locateActualPath([
                '/usr/share/misc/usb.ids',   // debian/ubuntu
                '/usr/share/usb.ids',        // opensuse
                '/usr/share/hwdata/usb.ids', // centos. maybe also redhat/fedora
            ]);

            if (!$usbIds) {
                return null;
            }

            $obj = new self();

            return $obj->result($mode, $usbIds);
        }

        throw new \InvalidArgumentException('Unknown mode "'.$mode.'"');
    }

    /**
     * Parse vendor and device names out of hardware ID files. Works for USB and PCI.
     */
    private function resolveIds(string $file, array $vendors, array $deviceKeys): array
    {
        $file = @\fopen($file, 'r');
        if (!$file) {
            return [];
        }
        $result = [];
        $remaining = \count($deviceKeys);
        $vendorId = null;
        $vendorName = null;
        while (($line = \fgets($file)) && $remaining > 0) {
            $line = \rtrim($line);
            if ('' === $line) {
                continue;
            }
            if ('#' === $line[0]) {
                continue;
            }
            if ("\t" !== $line[0]) {
                $vendorId = \substr($line, 0, 4);
                $vendorName = \substr($line, 6);
                // If we aren't looking for this vendor, skip parsing all of it
                if (!isset($vendors[$vendorId]) || !$vendorId || !$vendorName) {
                    $vendorId = null;
                    $vendorName = null;
                }
            } elseif ("\t" !== $line[1] && null !== $vendorId) {
                $deviceId = \substr($line, 1, 4);
                $deviceName = \substr($line, 7);
                if ($deviceId && $deviceName) {
                    $device_key = $vendorId.'-'.$deviceId;
                    if (isset($deviceKeys[$device_key])) {
                        $result[$device_key] = [$vendorName, $deviceName];
                        --$remaining;
                    }
                }
            }
        }
        \fclose($file);

        return $result;
    }

    /**
     * Get device and vendor IDs for USB devices on Linux.
     */
    private function fetchUsbIdsLinux(): array
    {
        $devices = [];
        $vendors = [];
        $speeds = [];
        foreach ((array) @\glob('/sys/bus/usb/devices/*', \GLOB_NOSORT) as $path) {
            // Avoid the same device artificially appearing more than once
            if (\str_contains($path, ':')) {
                continue;
            }

            // First try uevent
            if (\is_readable($path.'/uevent')
                && \preg_match('/^product=([^\/]+)\/([^\/]+)\/[^$]+$/m', \mb_strtolower(Common::getContents($path.'/uevent')), $match)) {
                $vendorId = \str_pad($match[1], 4, '0', \STR_PAD_LEFT);
                $deviceId = \str_pad($match[2], 4, '0', \STR_PAD_LEFT);
                $deviceKey = $vendorId.'-'.$deviceId;
                $vendors[$vendorId] = true;
                $devices[$deviceKey] = isset($devices[$deviceKey]) ? $devices[$deviceKey] + 1 : 1;
            } // And next modalias
            elseif (\is_readable($path.'/modalias')
                && \preg_match('/^usb:v([0-9A-Z]{4})p([0-9A-Z]{4})/', Common::getContents($path.'/modalias'), $match)) {
                $vendorId = \mb_strtolower($match[1]);
                $deviceId = \mb_strtolower($match[2]);
                $deviceKey = $vendorId.'-'.$deviceId;
                $vendors[$vendorId] = true;
                $devices[$deviceKey] = isset($devices[$deviceKey]) ? $devices[$deviceKey] + 1 : 1;
            } else {
                // Forget it
                continue;
            }

            // Also get speed
            $speed = (int) Common::getContents($path.'/speed', '0');
            if ($speed) {
                $speeds[$deviceKey] = $speed * 1000 * 1000;
            }
        }

        return [
            'vendors' => $vendors,
            'devices' => $devices,
            'speeds' => $speeds,
        ];
    }

    /**
     * Get device and vendor IDs for PCI devices on Linux.
     */
    private function fetchPciIdsLinux(): array
    {
        $vendors = [];
        $devices = [];
        foreach ((array) @\glob('/sys/bus/pci/devices/*', \GLOB_NOSORT) as $path) {
            // See if we can use simple vendor/device files and avoid taking time with regex
            if (($fDevice = Common::getContents($path.'/device', '')) && ($fVend = Common::getContents($path.'/vendor', ''))
                && '' !== $fDevice && '' !== $fVend) {
                [, $vendorId] = \explode('x', $fVend, 2);
                [, $deviceId] = \explode('x', $fDevice, 2);
                $deviceKey = $vendorId.'-'.$deviceId;
                $vendors[$vendorId] = true;
                $devices[$deviceKey] = isset($devices[$deviceKey]) ? $devices[$deviceKey] + 1 : 1;
            } // Try uevent nextly
            elseif (\is_readable($path.'/uevent')
                && \preg_match('/pci\_(?:subsys_)?id=(\w+):(\w+)/', \mb_strtolower(Common::getContents($path.'/uevent')), $match)) {
                [, $vendorId, $deviceId] = $match;
                $deviceKey = $vendorId.'-'.$deviceId;
                $vendors[$vendorId] = true;
                $devices[$deviceKey] = isset($devices[$deviceKey]) ? $devices[$deviceKey] + 1 : 1;
            } // Now for modalias
            elseif (\is_readable($path.'/modalias')
                && \preg_match('/^pci:v0{4}([0-9A-Z]{4})d0{4}([0-9A-Z]{4})/i', \mb_strtolower(Common::getContents($path.'/modalias')), $match)) {
                [, $vendorId, $deviceId] = $match;
                $deviceKey = $vendorId.'-'.$deviceId;
                $vendors[$vendorId] = true;
                $devices[$deviceKey] = isset($devices[$deviceKey]) ? $devices[$deviceKey] + 1 : 1;
            }
        }

        return [
            'vendors' => $vendors,
            'devices' => $devices,
            'speeds' => [],
        ];
    }

    /**
     * Get any USB or PCI devices present on the host system.
     */
    private function extractDevs(array $deviceIds, string $file): array
    {
        if (!\count($deviceIds)) {
            return [];
        }
        $vendors = $deviceIds['vendors'];
        $deviceKeys = $deviceIds['devices'];
        $speeds = $deviceIds['speeds'];
        $resolvedNames = $this->resolveIds($file, $vendors, $deviceKeys);

        $result = [];
        foreach ($deviceKeys as $key => $count) {
            if (isset($resolvedNames[$key])) {
                [$vendor, $device] = $resolvedNames[$key];
                $result[] = [
                    'vendor' => $vendor,
                    'device' => $device,
                    'count' => $count,
                    'speed' => $speeds[$key] ?? null];
            }
        }

        return $result;
    }

    /**
     * Compile and return USB and PCI devices in a sorted list.
     */
    private function result(string $mode, string $file): array
    {
        if (self::MODE_PCI === $mode) {
            $deviceIds = $this->fetchPciIdsLinux();
        } elseif (self::MODE_USB === $mode) {
            $deviceIds = $this->fetchUsbIdsLinux();
        } else {
            return [];
        }

        $allDevices = $this->extractDevs($deviceIds, $file);

        $sortType = [];
        $sortVendor = [];
        $sortDevice = [];

        foreach ($allDevices as $device) {
            $sortType[] = $device['type'];
            $sortVendor[] = $device['vendor'];
            $sortDevice[] = $device['device'];
        }

        \array_multisort($sortType, \SORT_ASC, $sortVendor, \SORT_ASC, $sortDevice, \SORT_ASC, $allDevices);

        return $allDevices;
    }
}
