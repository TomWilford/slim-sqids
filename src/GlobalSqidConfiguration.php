<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use RuntimeException;
use Sqids\Sqids;

/**
 * Provides a global, immutable instance of Sqids.
 *
 * This enables the configuration to be accessed within the HasSqidablePropertyTrait without requiring
 * individual classes to depend directly on Sqids.
 */
final class GlobalSqidConfiguration
{
    private static ?Sqids $sqids = null;

    /**
     * Sets the global Sqids instance.
     *
     * This method may only be called once. If the configuration has already been set, a RuntimeException
     * is thrown to enforce immutability.
     *
     * @param Sqids $sqids The Sqids instance to set.
     * @throws RuntimeException if the global configuration has already been established.
     */
    public static function set(Sqids $sqids): void
    {
        if (self::$sqids !== null) {
            throw new RuntimeException('Global Sqids configuration already set.');
        }
        self::$sqids = $sqids;
    }

    /**
     * Retrieves the global Sqids instance.
     *
     * If the configuration has not been set, a RuntimeException is thrown.
     *
     * @return Sqids The Sqids instance to be used for encoding/decoding.
     * @throws RuntimeException if the global configuration is not yet set.
     */
    public static function get(): Sqids
    {
        if (self::$sqids === null) {
            throw new RuntimeException('Global Sqids configuration not set.');
        }
        return self::$sqids;
    }
}
