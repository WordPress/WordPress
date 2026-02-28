<?php

declare (strict_types=1);
namespace Sentry\Serializer\EnvelopItems;

use Sentry\Event;
use Sentry\Profiling\Profile;
use Sentry\Util\JSON;
/**
 * @internal
 */
class ProfileItem implements \Sentry\Serializer\EnvelopItems\EnvelopeItemInterface
{
    public static function toEnvelopeItem(\Sentry\Event $event) : ?string
    {
        $header = ['type' => 'profile', 'content_type' => 'application/json'];
        $profile = $event->getSdkMetadata('profile');
        if (!$profile instanceof \Sentry\Profiling\Profile) {
            return null;
        }
        $payload = $profile->getFormattedData($event);
        if ($payload === null) {
            return null;
        }
        return \sprintf("%s\n%s", \Sentry\Util\JSON::encode($header), \Sentry\Util\JSON::encode($payload));
    }
}
