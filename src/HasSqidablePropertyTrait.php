<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use Sqids\Sqids;

/**
 * Trait that provides a getter for a Sqid encoded version of a designated property.
 *
 * To use this trait, mark the property you wish to encode with the #[SqidableProperty] attribute.
 * The trait will look for a Sqids instance on the current class and, if none is found,
 * it will fall back to the global Sqids configuration provided by GlobalSqidConfiguration.
 */
trait HasSqidablePropertyTrait
{
    private ?Sqids $sqids = null;

    /**
     * Retrieves a Sqids instance for encoding.
     *
     * This method first checks if a local Sqids instance is set on the class. If not,
     * it returns the global instance configured via GlobalSqidConfiguration.
     *
     * @return Sqids The Sqids instance to be used for encoding.
     */
    private function getSqids(): Sqids
    {
        return $this->sqids ?? GlobalSqidConfiguration::get();
    }

    /**
     * Returns the Sqid encoded value of the first property marked with #[SqidableProperty].
     *
     * This method uses reflection to scan the class's properties for the #[SqidableProperty] attribute.
     * If it finds such a property and the property has a value, the value is encoded using the Sqids instance,
     * and the encoded string is returned. If no matching property is found or its value is null, the method
     * returns null.
     *
     * @return string|null The encoded property value, or null if no suitable property is found.
     */
    public function getSqid(): ?string
    {
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(SqidableProperty::class);

            if (count($attributes) === 1) {
                if ($value = $property->getValue($this)) {
                    return $this->getSqids()->encode([$value]);
                }
            }
        }

        return null;
    }
}
