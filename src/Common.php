<?php

namespace Ginfo;

use Ginfo\Info\Service;

class Common
{
    /**
     * Certain files, specifically the pci/usb ids files, vary in location from
     * linux distro to linux distro. This function, when passed an array of
     * possible file location, picks the first it finds and returns it. When
     * none are found, it returns false.
     *
     * @param string[] $paths
     */
    public static function locateActualPath(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (\file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get a file's contents, or default to second param.
     *
     * @return string|mixed|null
     */
    public static function getContents(string $file, $default = null)
    {
        if (\file_exists($file) && \is_readable($file)) {
            $data = @\file_get_contents($file);
            if (false === $data) {
                return $default;
            }

            return \trim($data);
        }

        return $default;
    }

    /**
     * Like above, but in lines instead of a big string.
     *
     * @return string[]|mixed|null
     */
    public static function getLines(string $file, $default = null)
    {
        if (\file_exists($file) && \is_readable($file)) {
            $data = @\file($file, \FILE_SKIP_EMPTY_LINES);
            if (false === $data) {
                return $default;
            }

            return $data;
        }

        return $default;
    }

    /**
     * Like above, but parse as ini.
     *
     * @return array|mixed|null
     */
    public static function getIni(string $file, $default = null)
    {
        if (\file_exists($file) && \is_readable($file)) {
            $data = @\parse_ini_file($file);
            if (false === $data) {
                return $default;
            }

            return $data;
        }

        return $default;
    }

    /**
     * Prevent silly conditionals like if (in_array() || in_array() || in_array())
     * Poor man's python's any() on a list comprehension kinda.
     */
    public static function anyInArray(array $needles, array $haystack): bool
    {
        return \count(\array_intersect($needles, $haystack)) > 0;
    }

    /**
     * @return string[]
     */
    public static function parseKeyValueBlock(string $block, string $delimiter = ':'): array
    {
        $tmp = [];
        foreach (\explode("\n", $block) as $line) {
            if (\str_contains($line, $delimiter)) {
                [$key, $value] = \explode($delimiter, $line, 2);
                $tmp[\trim($key)] = \trim($value);
            }
        }

        return $tmp;
    }

    /**
     * @param Service[] $services
     */
    public static function searchService(array $services, string $serviceName, ?string $type = null): ?Service
    {
        foreach ($services as $service) {
            if ($service->getName() === $serviceName) {
                if ($type) {
                    if ($service->getType() === $type) {
                        return $service;
                    }
                } else {
                    return $service;
                }
            }
        }

        return null;
    }

    public static function convertHumanSizeToBytes(string $humanSize): ?float
    {
        $lastLetter = \substr($humanSize, -1);
        if (\is_numeric($lastLetter)) {
            return (float) $humanSize;
        }

        $size = \substr($humanSize, 0, -1);

        return match (\strtolower($lastLetter)) {
            'b' => (float) $size,
            'k' => (float) $size * 1024,
            'm' => (float) $size * 1024 * 1024,
            'g' => (float) $size * 1024 * 1024 * 1024,
            default => null,
        };
    }
}
