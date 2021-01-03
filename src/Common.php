<?php

namespace Ginfo;

use Ginfo\Info\Service;

class Common
{
    /**
     * Certain files, specifcally the pci/usb ids files, vary in location from
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
     * @param mixed $default
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
     * @param mixed $default
     *
     * @return string[]|mixed|null
     */
    public static function getLines(string $file, $default = null)
    {
        if (\file_exists($file) && \is_readable($file)) {
            return @\file($file, \FILE_SKIP_EMPTY_LINES);
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
            if (false !== \mb_strpos($line, $delimiter)) {
                [$key, $value] = \explode($delimiter, $line, 2);
                $tmp[\trim($key)] = \trim($value);
            }
        }

        return $tmp;
    }

    /**
     * @param Service[] $services
     */
    public static function searchService(array $services, string $serviceName): ?Service
    {
        $item = null;
        foreach ($services as $service) {
            if ($service->getName() === $serviceName) {
                $item = $service;
                break;
            }
        }

        return $item;
    }

    public static function convertHumanSizeToBytes(string $humanSize): ?float
    {
        $lastLetter = \mb_substr($humanSize, -1);
        if (\is_numeric($lastLetter)) {
            return (float) $humanSize;
        }

        $size = \mb_substr($humanSize, 0, -1);
        switch (\mb_strtolower($lastLetter)) {
            case 'b':
                return (float) $size;
                break;

            case 'k':
                return (float) $size * 1024;
                break;

            case 'm':
                return (float) $size * 1024 * 1024;
                break;

            case 'g':
                return (float) $size * 1024 * 1024 * 1024;
                break;
        }

        return null;
    }
}
