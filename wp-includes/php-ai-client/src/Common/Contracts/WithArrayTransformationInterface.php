<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common\Contracts;

/**
 * Interface for objects that support array transformation.
 *
 * @since 0.1.0
 *
 * @template TArrayShape of array<string, mixed>
 */
interface WithArrayTransformationInterface
{
    /**
     * Converts the object to an array representation.
     *
     * @since 0.1.0
     *
     * @return TArrayShape The array representation.
     */
    public function toArray(): array;
    /**
     * Creates an instance from array data.
     *
     * @since 0.1.0
     *
     * @param TArrayShape $array The array data.
     * @return self<TArrayShape> The created instance.
     */
    public static function fromArray(array $array): self;
    /**
     * Checks if the array is a valid shape for this object.
     *
     * @since 0.1.0
     *
     * @param array<mixed> $array The array to check.
     * @return bool True if the array is a valid shape.
     * @phpstan-assert-if-true TArrayShape $array
     */
    public static function isArrayShape(array $array): bool;
}
