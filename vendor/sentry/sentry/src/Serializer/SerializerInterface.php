<?php

declare(strict_types=1);

namespace Sentry\Serializer;

/**
 * Basic serializer for the event data.
 */
interface SerializerInterface
{
    /**
     * Serialize an object (recursively) into something safe to be sent in an Event.
     *
     * @param mixed $value
     *
     * @return string|bool|float|int|object|mixed[]|null
     */
    public function serialize($value);
}
