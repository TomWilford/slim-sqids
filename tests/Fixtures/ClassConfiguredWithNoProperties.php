<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures;

use TomWilford\SlimSqids\HasSqidablePropertyTrait;

class ClassConfiguredWithNoProperties
{
    use HasSqidablePropertyTrait;

    public function __construct(private int $id)
    {
        //
    }
}
