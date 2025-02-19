<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\TestCase;

use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sqids\Sqids;
use TomWilford\SlimSqids\GlobalSqidConfiguration;

#[UsesClass(GlobalSqidConfiguration::class)]
class GlobalSqidConfigurationTest extends TestCase
{
    public function testSqidsConfigurationCannotBeRetrievedIfNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        GlobalSqidConfiguration::get();
    }

    public function testSqidsConfigurationIsImmutable(): void
    {
        $sqids = new Sqids();
        GlobalSqidConfiguration::set($sqids);

        $this->expectException(\RuntimeException::class);
        GlobalSqidConfiguration::set($sqids);
    }
}
