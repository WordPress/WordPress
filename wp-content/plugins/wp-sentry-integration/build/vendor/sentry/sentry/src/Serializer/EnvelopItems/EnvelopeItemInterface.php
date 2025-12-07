<?php

declare (strict_types=1);
namespace Sentry\Serializer\EnvelopItems;

use Sentry\Event;
/**
 * @internal
 */
interface EnvelopeItemInterface
{
    public static function toEnvelopeItem(\Sentry\Event $event) : ?string;
}
