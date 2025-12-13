<?php

declare (strict_types=1);
namespace Sentry\Serializer;

use Sentry\Event;
use Sentry\EventType;
use Sentry\Options;
use Sentry\Serializer\EnvelopItems\CheckInItem;
use Sentry\Serializer\EnvelopItems\EventItem;
use Sentry\Serializer\EnvelopItems\LogsItem;
use Sentry\Serializer\EnvelopItems\ProfileItem;
use Sentry\Serializer\EnvelopItems\TransactionItem;
use Sentry\Tracing\DynamicSamplingContext;
use Sentry\Util\JSON;
/**
 * This is a simple implementation of a serializer that takes in input an event
 * object and returns a serialized string ready to be sent off to Sentry.
 *
 * @internal
 */
final class PayloadSerializer implements \Sentry\Serializer\PayloadSerializerInterface
{
    /**
     * @var Options The SDK client options
     */
    private $options;
    public function __construct(\Sentry\Options $options)
    {
        $this->options = $options;
    }
    /**
     * {@inheritdoc}
     */
    public function serialize(\Sentry\Event $event) : string
    {
        // @see https://develop.sentry.dev/sdk/envelopes/#envelope-headers
        $envelopeHeader = ['event_id' => (string) $event->getId(), 'sent_at' => \gmdate('Y-m-d\\TH:i:s\\Z'), 'dsn' => (string) $this->options->getDsn(), 'sdk' => $event->getSdkPayload()];
        $dynamicSamplingContext = $event->getSdkMetadata('dynamic_sampling_context');
        if ($dynamicSamplingContext instanceof \Sentry\Tracing\DynamicSamplingContext) {
            $entries = $dynamicSamplingContext->getEntries();
            if (!empty($entries)) {
                $envelopeHeader['trace'] = $entries;
            }
        }
        $items = [];
        switch ($event->getType()) {
            case \Sentry\EventType::event():
                $items[] = \Sentry\Serializer\EnvelopItems\EventItem::toEnvelopeItem($event);
                break;
            case \Sentry\EventType::transaction():
                $items[] = \Sentry\Serializer\EnvelopItems\TransactionItem::toEnvelopeItem($event);
                if ($event->getSdkMetadata('profile') !== null) {
                    $items[] = \Sentry\Serializer\EnvelopItems\ProfileItem::toEnvelopeItem($event);
                }
                break;
            case \Sentry\EventType::checkIn():
                $items[] = \Sentry\Serializer\EnvelopItems\CheckInItem::toEnvelopeItem($event);
                break;
            case \Sentry\EventType::logs():
                $items[] = \Sentry\Serializer\EnvelopItems\LogsItem::toEnvelopeItem($event);
                break;
        }
        return \sprintf("%s\n%s", \Sentry\Util\JSON::encode($envelopeHeader), \implode("\n", \array_filter($items)));
    }
}
