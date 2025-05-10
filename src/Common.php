<?php

namespace Ginfo;

/**
 * @internal
 *
 * @deprecated
 */
final readonly class Common
{
    /**
     * Get a file's contents, or default to second param.
     *
     * @deprecated
     */
    public static function getContents(string $file, ?string $default = null): ?string
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
     * @deprecated
     *
     * @return array<string, string>
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
}
