<?php

declare(strict_types=1);

namespace Sentry\Serializer;

use Sentry\Event;

/**
 * This interface defines the contract for the classes willing to serialize an
 * event object to a format suitable for sending over the wire to Sentry.
 */
interface PayloadSerializerInterface
{
    /**
     * Serializes the given event object into a string.
     *
     * @param Event $event The event object
     */
    public function serialize(Event $event): string;
}
