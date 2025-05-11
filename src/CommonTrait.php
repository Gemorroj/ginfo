<?php

namespace Ginfo;

/**
 * @internal
 */
trait CommonTrait
{
    /**
     * Get a file's contents, or default to second param.
     */
    protected static function getContents(string $file, ?string $default = null): ?string
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
     * @return array<string, string>
     */
    protected static function parseKeyValueBlock(string $block, string $delimiter = ':'): array
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
