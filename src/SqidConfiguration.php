<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Sqids\Sqids;

final class SqidConfiguration
{
    private static ?Sqids $sqids = null;
    private static ?ContainerInterface $container = null;

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function getContainer(): ?ContainerInterface
    {
        return self::$container;
    }

    public static function setSqidsInstance(Sqids $sqids): void
    {
        self::$sqids = $sqids;
    }

    /**
     * @return Sqids
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
     */
    public static function getSqidsInstance(): Sqids
    {
        if (self::$sqids === null) {
            $container = SqidConfiguration::getContainer();
            if ($container !== null) {
                $sqids = SqidConfiguration::getContainer()?->get(Sqids::class);
                if ($sqids instanceof Sqids) {
                    self::$sqids = $sqids;
                }
            }
        }
        if (self::$sqids === null) {
            throw new RuntimeException('Sqids service not found');
        }

        return self::$sqids;
    }
}
