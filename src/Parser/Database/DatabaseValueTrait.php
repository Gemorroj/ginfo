<?php

namespace Ginfo\Parser\Database;

/**
 * Helpers for values returned by database drivers: they are always strings, but may be NULL
 * or absent (column missing in this server version).
 *
 * @internal
 */
trait DatabaseValueTrait
{
    /**
     * Converts a nullable numeric value, keeping NULL as NULL.
     *
     * Use when NULL is a meaningful state of the data source (e.g. a counter that was never measured),
     * and the DTO property is nullable as well.
     */
    protected static function toNumberOrNull(?string $value): int|float|null
    {
        if (null === $value) {
            return null;
        }
        if ($value > \PHP_INT_MAX) {
            return (float) $value;
        }
        if ((string) (int) $value !== $value) {
            return (float) $value;
        }

        return (int) $value;
    }

    /**
     * Converts a nullable numeric value, treating NULL as 0.
     *
     * Use for counters and sizes (the DTO property is not nullable): NULL means "nothing happened"
     * or "this server version does not track it", which is reported as 0.
     */
    protected static function toNumberOrZero(?string $value): int|float
    {
        return self::toNumberOrNull($value) ?? 0;
    }

    /**
     * Converts a nullable date value, keeping NULL as NULL.
     */
    protected static function toDateOrNull(?string $value): ?\DateTimeImmutable
    {
        return $value ? new \DateTimeImmutable($value) : null;
    }
}
