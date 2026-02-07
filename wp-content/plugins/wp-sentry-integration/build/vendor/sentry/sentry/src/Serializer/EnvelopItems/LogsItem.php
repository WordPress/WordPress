<?php

declare (strict_types=1);
namespace Sentry\Serializer\EnvelopItems;

use Sentry\Attributes\Attribute;
use Sentry\Event;
use Sentry\EventType;
use Sentry\Logs\Log;
use Sentry\Util\JSON;
/**
 * @internal
 */
class LogsItem implements \Sentry\Serializer\EnvelopItems\EnvelopeItemInterface
{
    public static function toEnvelopeItem(\Sentry\Event $event) : string
    {
        $logs = $event->getLogs();
        $header = ['type' => (string) \Sentry\EventType::logs(), 'item_count' => \count($logs), 'content_type' => 'application/vnd.sentry.items.log+json'];
        return \sprintf("%s\n%s", \Sentry\Util\JSON::encode($header), \Sentry\Util\JSON::encode(['items' => \array_map(static function (\Sentry\Logs\Log $log) : array {
            return ['timestamp' => $log->getTimestamp(), 'trace_id' => $log->getTraceId(), 'level' => (string) $log->getLevel(), 'body' => $log->getBody(), 'attributes' => \array_map(static function (\Sentry\Attributes\Attribute $attribute) : array {
                return ['type' => $attribute->getType(), 'value' => $attribute->getValue()];
            }, $log->attributes()->all())];
        }, $logs)]));
    }
}
