<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use Sqids\Sqids;

final class GlobalSqidConfiguration
{
    private static ?Sqids $sqids = null;

    public static function set(Sqids $sqids): void
    {
        if (self::$sqids !== null) {
            throw new \RuntimeException('Global Sqids configuration already set.');
        }
        self::$sqids = $sqids;
    }

    public static function get(): Sqids
    {
        if (self::$sqids === null) {
            throw new \RuntimeException('Global Sqids configuration not set.');
        }
        return self::$sqids;
    }
}
