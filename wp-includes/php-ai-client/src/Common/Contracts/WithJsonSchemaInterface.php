<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common\Contracts;

/**
 * Interface for objects that can provide their JSON schema representation.
 *
 * This interface is implemented by DTOs to provide a consistent way to retrieve
 * their JSON schema for validation and serialization purposes.
 *
 * @since 0.1.0
 */
interface WithJsonSchemaInterface
{
    /**
     * Gets the JSON schema representation of the object.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed> The JSON schema as an associative array.
     */
    public static function getJsonSchema(): array;
}
