<?php

declare(strict_types=1);

namespace Sentry\Serializer\EnvelopItems;

use Sentry\Event;
use Sentry\EventType;
use Sentry\Serializer\Traits\BreadcrumbSeralizerTrait;
use Sentry\Tracing\Span;
use Sentry\Tracing\TransactionMetadata;
use Sentry\Util\JSON;

/**
 * @internal
 *
 * @phpstan-type MetricsSummary array{
 *     min: int|float,
 *     max: int|float,
 *     sum: int|float,
 *     count: int,
 *     tags: array<string>,
 * }
 */
class TransactionItem implements EnvelopeItemInterface
{
    use BreadcrumbSeralizerTrait;

    public static function toEnvelopeItem(Event $event): string
    {
        $header = [
            'type' => (string) EventType::transaction(),
            'content_type' => 'application/json',
        ];

        $payload = [
            'timestamp' => $event->getTimestamp(),
            'platform' => 'php',
            'sdk' => $event->getSdkPayload(),
        ];

        if ($event->getStartTimestamp() !== null) {
            $payload['start_timestamp'] = $event->getStartTimestamp();
        }

        if ($event->getLevel() !== null) {
            $payload['level'] = (string) $event->getLevel();
        }

        if ($event->getTransaction() !== null) {
            $payload['transaction'] = $event->getTransaction();
        }

        if ($event->getServerName() !== null) {
            $payload['server_name'] = $event->getServerName();
        }

        if ($event->getRelease() !== null) {
            $payload['release'] = $event->getRelease();
        }

        if ($event->getEnvironment() !== null) {
            $payload['environment'] = $event->getEnvironment();
        }

        if (!empty($event->getFingerprint())) {
            $payload['fingerprint'] = $event->getFingerprint();
        }

        if (!empty($event->getModules())) {
            $payload['modules'] = $event->getModules();
        }

        if (!empty($event->getExtra())) {
            $payload['extra'] = $event->getExtra();
        }

        if (!empty($event->getTags())) {
            $payload['tags'] = $event->getTags();
        }

        $user = $event->getUser();
        if ($user !== null) {
            $payload['user'] = array_merge($user->getMetadata(), [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'ip_address' => $user->getIpAddress(),
                'segment' => $user->getSegment(),
            ]);
        }

        $osContext = $event->getOsContext();
        if ($osContext !== null) {
            $payload['contexts']['os'] = [
                'name' => $osContext->getName(),
                'version' => $osContext->getVersion(),
                'build' => $osContext->getBuild(),
                'kernel_version' => $osContext->getKernelVersion(),
            ];
        }

        $runtimeContext = $event->getRuntimeContext();
        if ($runtimeContext !== null) {
            $payload['contexts']['runtime'] = [
                'name' => $runtimeContext->getName(),
                'sapi' => $runtimeContext->getSAPI(),
                'version' => $runtimeContext->getVersion(),
            ];
        }

        if (!empty($event->getContexts())) {
            $payload['contexts'] = array_merge($payload['contexts'] ?? [], $event->getContexts());
        }

        if (!empty($event->getBreadcrumbs())) {
            $payload['breadcrumbs']['values'] = array_map([self::class, 'serializeBreadcrumb'], $event->getBreadcrumbs());
        }

        if (!empty($event->getRequest())) {
            $payload['request'] = $event->getRequest();
        }

        $payload['spans'] = array_values(array_map([self::class, 'serializeSpan'], $event->getSpans()));

        $transactionMetadata = $event->getSdkMetadata('transaction_metadata');
        if ($transactionMetadata instanceof TransactionMetadata) {
            $payload['transaction_info']['source'] = (string) $transactionMetadata->getSource();
        }

        return \sprintf("%s\n%s", JSON::encode($header), JSON::encode($payload));
    }

    /**
     * @return array<string, mixed>
     *
     * @psalm-return array{
     *     span_id: string,
     *     trace_id: string,
     *     parent_span_id?: string,
     *     start_timestamp: float,
     *     timestamp?: float,
     *     status?: string,
     *     description?: string,
     *     op?: string,
     *     data?: array<string, mixed>,
     *     tags?: array<string, string>
     *     _metrics_summary?: array<string, mixed>
     * }
     */
    protected static function serializeSpan(Span $span): array
    {
        $result = [
            'span_id' => (string) $span->getSpanId(),
            'trace_id' => (string) $span->getTraceId(),
            'start_timestamp' => $span->getStartTimestamp(),
            'origin' => $span->getOrigin() ?? 'manual',
        ];

        if ($span->getParentSpanId() !== null) {
            $result['parent_span_id'] = (string) $span->getParentSpanId();
        }

        if ($span->getEndTimestamp() !== null) {
            $result['timestamp'] = $span->getEndTimestamp();
        }

        if ($span->getStatus() !== null) {
            $result['status'] = (string) $span->getStatus();
        }

        if ($span->getDescription() !== null) {
            $result['description'] = $span->getDescription();
        }

        if ($span->getOp() !== null) {
            $result['op'] = $span->getOp();
        }

        if (!empty($span->getData())) {
            $result['data'] = $span->getData();
        }

        if (!empty($span->getTags())) {
            $result['tags'] = $span->getTags();
        }

        return $result;
    }
}
