<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures;

use TomWilford\SlimSqids\HasSqidablePropertyTrait;
use TomWilford\SlimSqids\SqidableProperty;

class ClassConfiguredWithMultipleProperties
{
    use HasSqidablePropertyTrait;

    public function __construct(
        #[SqidableProperty]
        private int $id,
        #[SqidableProperty]
        private int $otherId
    ) {
        //
    }
}
