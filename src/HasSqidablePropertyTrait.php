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
    private ?Sqids $sqidsConfiguration = null;

    /**
     * Retrieves a Sqids instance for encoding.
     *
     * This method first checks if a local Sqids instance is set on the class. If not,
     * it returns the global instance configured via GlobalSqidConfiguration.
     *
     * @return Sqids The Sqids instance to be used for encoding.
     */
    private function getSqidsConfiguration(): Sqids
    {
        return $this->sqidsConfiguration ?? GlobalSqidConfiguration::get();
    }

    /**
     * Returns the Sqid encoded value of all properties marked with #[SqidableProperty].
     *
     * This method uses reflection to scan the class's properties for the #[SqidableProperty] attribute.
     * If it finds such a property and the property has a value, the value is encoded using the Sqids instance,
     * and the encoded string is added to a return array. If no matching properties are found or if a properties value
     * is null, nothing is added to the array.
     *
     * A local cache stores the property names that are marked with #[SqidableProperty] to reduce the amount of times
     * we need to retrieve the sqidable properties via reflection per class.
     *
     * @return array<string> An array of propertyName => encodedSqid
     */
    public function getAllSqids(): array
    {
        static $cache = [];
        $class = static::class;

        if (!array_key_exists($class, $cache)) {
            $cache[$class] = [];
            $reflection = new \ReflectionClass($this);
            foreach ($reflection->getProperties() as $property) {
                if (count($property->getAttributes(SqidableProperty::class)) === 1) {
                    $cache[$class][] = $property->getName();
                }
            }
        }

        $result = [];
        $sqids = $this->getSqidsConfiguration();

        foreach ($cache[$class] as $propertyName) {
            $value = $this->{$propertyName};
            if ($value !== null) {
                $result[$propertyName] = $sqids->encode([$value]);
            }
        }

        return $result;
    }

    /**
     * Returns the first property marked with #[SqidableProperty] for convenience when working with the primary/only id
     * of a class.
     * @return string|null
     */
    public function getSqid(): ?string
    {
        return array_values($this->getAllSqids())[0] ?? null;
    }
}
