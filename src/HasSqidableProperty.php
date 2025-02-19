<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sqids\Sqids;

class HasSqidableProperty
{
    private function getSqids(): Sqids
    {
        return GlobalSqidConfiguration::get();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getSqid(): ?string
    {
        $reflection = new \ReflectionClass($this);

        $value = null;
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(SqidableProperty::class);

            if (count($attributes) === 1) {
                $value = $property->getValue($this);
            }
        }

        if ($value !== null) {
            $value = $this->getSqids()->encode([$value]);
        }

        return $value;
    }
}
