<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures\Entity;

use Sqids\Sqids;
use TomWilford\SlimSqids\HasSqidablePropertyTrait;
use TomWilford\SlimSqids\SqidableProperty;

class ClassWithInjectedSqidsInstance
{
    use HasSqidablePropertyTrait;

    #[SqidableProperty]
    private int $id;

    public function __construct(Sqids $sqids)
    {
        $this->sqidsConfiguration = $sqids;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
