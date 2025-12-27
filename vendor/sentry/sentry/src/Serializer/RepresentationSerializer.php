<?php

declare(strict_types=1);

namespace Sentry\Serializer;

/**
 * Serializes a value into a representation that should reasonably suggest
 * both the type and value, and be serializable into JSON.
 */
class RepresentationSerializer extends AbstractSerializer implements RepresentationSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function representationSerialize($value)
    {
        $value = $this->serializeRecursively($value);

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return $value;
    }

    /**
     * This method is overridden to return even basic types as strings.
     *
     * @param mixed $value The value that needs to be serialized
     *
     * @return string
     */
    protected function serializeValue($value)
    {
        if ($value === null) {
            return 'null';
        }

        if ($value === false) {
            return 'false';
        }

        if ($value === true) {
            return 'true';
        }

        if (\is_float($value) && (int) $value == $value) {
            return $value . '.0';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return (string) parent::serializeValue($value);
    }
}
