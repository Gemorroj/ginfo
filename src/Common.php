<?php

/**
 * This file is part of Linfo (c) 2014 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo.    If not, see <http://www.gnu.org/licenses/>.
 */

namespace Linfo;

class Common
{
    /**
     * Certain files, specifcally the pci/usb ids files, vary in location from
     * linux distro to linux distro. This function, when passed an array of
     * possible file location, picks the first it finds and returns it. When
     * none are found, it returns false
     * @param string[] $paths
     * @return null|string
     */
    public static function locateActualPath(array $paths) : ?string
    {
        foreach ($paths as $path) {
            if (\file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get a file's contents, or default to second param
     *
     * @param string $file
     * @param mixed $default
     * @return string|mixed|null
     */
    public static function getContents(string $file, $default = null)
    {
        if (\file_exists($file) && \is_readable($file)) {
            return \trim(\file_get_contents($file));
        }
        return $default;
    }

    /**
     * Like above, but in lines instead of a big string
     * @param string $file
     * @param mixed $default
     * @return string[]|mixed|null
     */
    public static function getLines(string $file, $default = null)
    {
        if (\file_exists($file) && \is_readable($file)) {
            return \file($file, \FILE_SKIP_EMPTY_LINES);
        }
        return $default;
    }


    /**
     * Prevent silly conditionals like if (in_array() || in_array() || in_array())
     * Poor man's python's any() on a list comprehension kinda
     * @param array $needles
     * @param array $haystack
     * @return bool
     */
    public static function anyInArray(array $needles, array $haystack) : bool
    {
        return \count(\array_intersect($needles, $haystack)) > 0;
    }


    /**
     * @param string $block
     * @param string $delimiter
     * @return string[]
     */
    public static function parseKeyValueBlock(string $block, string $delimiter = ':') : array
    {
        $tmp = [];
        foreach (\explode("\n", $block) as $line) {
            if (false !== \mb_strpos($line, $delimiter)) {
                @list($key, $value) = \explode(':', $line, 2);
                $tmp[\trim($key)] = \trim($value);
            }
        }
        return $tmp;
    }
}
