<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sqids\Sqids;
use TomWilford\SlimSqids\GlobalSqidConfiguration;
use TomWilford\SlimSqids\HasSqidablePropertyTrait;
use TomWilford\SlimSqids\Tests\Fixtures\Entity\ClassConfiguredWithMultipleProperties;
use TomWilford\SlimSqids\Tests\Fixtures\Entity\ClassConfiguredWithNoProperties;
use TomWilford\SlimSqids\Tests\Fixtures\Entity\ClassConfiguredWithSingleProperty;
use TomWilford\SlimSqids\Tests\Fixtures\Entity\ClassWithInjectedSqidsInstance;

#[CoversClass(HasSqidablePropertyTrait::class)]
#[UsesClass(Sqids::class)]
#[UsesClass(GlobalSqidConfiguration::class)]
class HasSqidablePropertyTest extends TestCase
{
    public function setUp(): void
    {
        try {
            GlobalSqidConfiguration::get();
        } catch (\RuntimeException $exception) {
            GlobalSqidConfiguration::set(new Sqids());
        }
    }

    public function testGetSqidEncodesPropertySpecifiedByAttribute()
    {
        $sqids = new Sqids();
        $expectedResult = $sqids->encode([1]);

        $sut = new ClassConfiguredWithSingleProperty(1);

        $this->assertSame($expectedResult, $sut->getSqid());
    }

    public function testGetSqidReturnsFirstPropertyWhenUsedMultipleTimes()
    {
        $sqids = new Sqids();
        $expectedResult = $sqids->encode([1]);

        $sut = new ClassConfiguredWithMultipleProperties(1, 2);

        $this->assertSame($expectedResult, $sut->getSqid());
    }

    public function testGetSqidReturnsNullWhenAttributeNotSet()
    {
        $sut = new ClassConfiguredWithNoProperties(1);

        $this->assertNull($sut->getSqid());
    }

    public function testGetSqidWorksWithInjectedInstanceOfSqids()
    {
        $sqids = new Sqids();
        $expectedResult = $sqids->encode([1]);
        $sut = new ClassWithInjectedSqidsInstance($sqids);

        $sut->setId(1);

        $this->assertSame($expectedResult, $sut->getSqid());
    }

    public function testGetAllSqidsEncodesPropertySpecifiedByAttribute()
    {
        $sqids = new Sqids();
        $expectedResult = [
            'id' => $sqids->encode([1])
        ];

        $sut = new ClassConfiguredWithSingleProperty(id: 1);

        $this->assertSame($expectedResult, $sut->getAllSqids());
    }

    public function testGetAllSqidsReturnsAllSqidableProperties()
    {
        $sqids = new Sqids();
        $expectedResult = [
            'id' => $sqids->encode([1]),
            'otherId' => $sqids->encode([2])
        ];

        $sut = new ClassConfiguredWithMultipleProperties(id: 1, otherId: 2);

        $this->assertSame($expectedResult, $sut->getAllSqids());
    }

    public function testGetAllSqidsReturnsEmptyArrayWhenAttributeNotSet()
    {
        $sut = new ClassConfiguredWithNoProperties(1);

        $this->assertEmpty($sut->getAllSqids());
    }
}
