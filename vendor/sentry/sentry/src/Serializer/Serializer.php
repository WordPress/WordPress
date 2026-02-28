<?php

declare(strict_types=1);

namespace Sentry\Serializer;

/**
 * Basic serializer for the event data.
 */
class Serializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize($value)
    {
        return $this->serializeRecursively($value);
    }
}
