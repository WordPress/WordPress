<?php

declare(strict_types=1);

namespace Sentry\Attributes;

/**
 * @phpstan-import-type AttributeValue from Attribute
 */
class AttributeBag
{
    /**
     * @var array<string, Attribute>
     */
    private $attributes = [];

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): self
    {
        $attribute = $value instanceof Attribute
            ? $value
            : Attribute::tryFromValue($value);

        if ($attribute !== null) {
            $this->attributes[$key] = $attribute;
        }

        return $this;
    }

    public function get(string $key): ?Attribute
    {
        return $this->attributes[$key] ?? null;
    }

    public function forget(string $key): self
    {
        unset($this->attributes[$key]);

        return $this;
    }

    /**
     * @return array<string, Attribute>
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * Get a simplified representation of the attributes as a key-value array, main purpose is for logging output.
     *
     * @return array<string, AttributeValue>
     */
    public function toSimpleArray(): array
    {
        return array_map(static function (Attribute $attribute) {
            return $attribute->getValue();
        }, $this->attributes);
    }
}
