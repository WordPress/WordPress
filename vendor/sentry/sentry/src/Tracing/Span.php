<?php

declare(strict_types=1);

namespace Sentry\Tracing;

use Sentry\EventId;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Sentry\Unit;

/**
 * This class stores all the information about a span.
 *
 * @phpstan-type MetricsSummary array{
 *     min: int|float,
 *     max: int|float,
 *     sum: int|float,
 *     count: int,
 *     tags: array<string>,
 * }
 */
class Span
{
    /**
     * Maximum number of flags allowed. We only track the first flags set.
     *
     * @internal
     */
    public const MAX_FLAGS = 10;

    /**
     * @var SpanId Span ID
     */
    protected $spanId;

    /**
     * @var TraceId Trace ID
     */
    protected $traceId;

    /**
     * @var string|null Description of the span
     */
    protected $description;

    /**
     * @var string|null Operation of the span
     */
    protected $op;

    /**
     * @var SpanStatus|null Completion status of the span
     */
    protected $status;

    /**
     * @var SpanId|null ID of the parent span
     */
    protected $parentSpanId;

    /**
     * @var bool|null Has the sample decision been made?
     */
    protected $sampled;

    /**
     * @var array<string, string> A List of tags associated to this span
     */
    protected $tags = [];

    /**
     * @var array<string, bool> A List of flags associated to this span
     */
    protected $flags = [];

    /**
     * @var array<string, mixed> An arbitrary mapping of additional metadata
     */
    protected $data = [];

    /**
     * @var float Timestamp in seconds (epoch time) indicating when the span started
     */
    protected $startTimestamp;

    /**
     * @var float|null Timestamp in seconds (epoch time) indicating when the span ended
     */
    protected $endTimestamp;

    /**
     * @var SpanRecorder|null Reference instance to the {@see SpanRecorder}
     */
    protected $spanRecorder;

    /**
     * @var Transaction|null The transaction containing this span
     */
    protected $transaction;

    /**
     * @var string|null The trace origin of the span. If no origin is set, the span is considered to be "manual".
     *
     * @see https://develop.sentry.dev/sdk/performance/trace-origin/
     */
    protected $origin;

    /**
     * Constructor.
     *
     * @param SpanContext|null $context The context to create the span with
     *
     * @internal
     */
    public function __construct(?SpanContext $context = null)
    {
        if ($context === null) {
            $this->traceId = TraceId::generate();
            $this->spanId = SpanId::generate();
            $this->startTimestamp = microtime(true);

            return;
        }

        $this->traceId = $context->getTraceId() ?? TraceId::generate();
        $this->spanId = $context->getSpanId() ?? SpanId::generate();
        $this->startTimestamp = $context->getStartTimestamp() ?? microtime(true);
        $this->parentSpanId = $context->getParentSpanId();
        $this->description = $context->getDescription();
        $this->op = $context->getOp();
        $this->status = $context->getStatus();
        $this->sampled = $context->getSampled();
        $this->tags = $context->getTags();
        $this->data = $context->getData();
        $this->endTimestamp = $context->getEndTimestamp();
        $this->origin = $context->getOrigin();
    }

    /**
     * Sets the ID of the span.
     *
     * @param SpanId $spanId The ID
     */
    public function setSpanId(SpanId $spanId): self
    {
        $this->spanId = $spanId;

        return $this;
    }

    /**
     * Gets the ID that determines which trace the span belongs to.
     */
    public function getTraceId(): TraceId
    {
        return $this->traceId;
    }

    /**
     * Sets the ID that determines which trace the span belongs to.
     *
     * @param TraceId $traceId The ID
     *
     * @return $this
     */
    public function setTraceId(TraceId $traceId)
    {
        $this->traceId = $traceId;

        return $this;
    }

    /**
     * Gets the ID that determines which span is the parent of the current one.
     */
    public function getParentSpanId(): ?SpanId
    {
        return $this->parentSpanId;
    }

    /**
     * Sets the ID that determines which span is the parent of the current one.
     *
     * @param SpanId|null $parentSpanId The ID
     *
     * @return $this
     */
    public function setParentSpanId(?SpanId $parentSpanId)
    {
        $this->parentSpanId = $parentSpanId;

        return $this;
    }

    /**
     * Gets the timestamp representing when the measuring started.
     */
    public function getStartTimestamp(): float
    {
        return $this->startTimestamp;
    }

    /**
     * Sets the timestamp representing when the measuring started.
     *
     * @param float $startTimestamp The timestamp
     *
     * @return $this
     */
    public function setStartTimestamp(float $startTimestamp)
    {
        $this->startTimestamp = $startTimestamp;

        return $this;
    }

    /**
     * Gets the timestamp representing when the measuring finished.
     */
    public function getEndTimestamp(): ?float
    {
        return $this->endTimestamp;
    }

    /**
     * Gets a description of the span's operation, which uniquely identifies
     * the span but is consistent across instances of the span.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets a description of the span's operation, which uniquely identifies
     * the span but is consistent across instances of the span.
     *
     * @param string|null $description The description
     *
     * @return $this
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets a short code identifying the type of operation the span is measuring.
     */
    public function getOp(): ?string
    {
        return $this->op;
    }

    /**
     * Sets a short code identifying the type of operation the span is measuring.
     *
     * @param string|null $op The short code
     *
     * @return $this
     */
    public function setOp(?string $op)
    {
        $this->op = $op;

        return $this;
    }

    /**
     * Gets the status of the span/transaction.
     */
    public function getStatus(): ?SpanStatus
    {
        return $this->status;
    }

    /**
     * Sets the status of the span/transaction.
     *
     * @param SpanStatus|null $status The status
     *
     * @return $this
     */
    public function setStatus(?SpanStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Sets the HTTP status code and the status of the span/transaction.
     *
     * @param int $statusCode The HTTP status code
     *
     * @return $this
     */
    public function setHttpStatus(int $statusCode)
    {
        SentrySdk::getCurrentHub()->configureScope(function (Scope $scope) use ($statusCode) {
            $scope->setContext('response', [
                'status_code' => $statusCode,
            ]);
        });

        $status = SpanStatus::createFromHttpStatusCode($statusCode);

        if ($status !== SpanStatus::unknownError()) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Gets a map of tags for this event.
     *
     * @return array<string, string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Sets a map of tags for this event. This method will merge the given tags with
     * the existing ones.
     *
     * @param array<string, string> $tags The tags
     *
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = array_merge($this->tags, $tags);

        return $this;
    }

    /**
     * Sets a feature flag associated to this span.
     *
     * @return $this
     */
    public function setFlag(string $key, bool $result)
    {
        if (\count($this->flags) < self::MAX_FLAGS) {
            $this->flags[$key] = $result;
        }

        return $this;
    }

    /**
     * Gets the ID of the span.
     */
    public function getSpanId(): SpanId
    {
        return $this->spanId;
    }

    /**
     * Gets the flag determining whether this span should be sampled or not.
     */
    public function getSampled(): ?bool
    {
        return $this->sampled;
    }

    /**
     * Sets the flag determining whether this span should be sampled or not.
     *
     * @param bool $sampled Whether to sample or not this span
     *
     * @return $this
     */
    public function setSampled(?bool $sampled)
    {
        $this->sampled = $sampled;

        return $this;
    }

    /**
     * Gets a map of arbitrary data or a specific key from the map of data attached to this span.
     *
     * @param string|null $key     Select a specific key from the data to return the value of
     * @param mixed       $default When the $key is not found, return this value
     *
     * @return ($key is null ? array<string, mixed> : mixed|null)
     */
    public function getData(?string $key = null, $default = null)
    {
        if ($key === null) {
            $data = $this->data;

            foreach ($this->flags as $flagKey => $flagValue) {
                $data["flag.evaluation.{$flagKey}"] = $flagValue;
            }

            return $data;
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Sets a map of arbitrary data. This method will merge the given data with the existing one.
     *
     * @param array<string, mixed> $data The data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Gets the data in a format suitable for storage in the "trace" context.
     *
     * @return array<string, mixed>
     *
     * @psalm-return array{
     *     data?: array<string, mixed>,
     *     description?: string,
     *     op?: string,
     *     parent_span_id?: string,
     *     span_id: string,
     *     status?: string,
     *     tags?: array<string, string>,
     *     trace_id: string,
     *     origin: string,
     * }
     */
    public function getTraceContext(): array
    {
        $result = [
            'span_id' => (string) $this->spanId,
            'trace_id' => (string) $this->traceId,
            'origin' => $this->origin ?? 'manual',
        ];

        if ($this->parentSpanId !== null) {
            $result['parent_span_id'] = (string) $this->parentSpanId;
        }

        if ($this->description !== null) {
            $result['description'] = $this->description;
        }

        if ($this->op !== null) {
            $result['op'] = $this->op;
        }

        if ($this->status !== null) {
            $result['status'] = (string) $this->status;
        }

        if (!empty($this->data)) {
            $result['data'] = $this->data;
        }

        if (!empty($this->tags)) {
            $result['tags'] = $this->tags;
        }

        return $result;
    }

    /**
     * Sets the finish timestamp on the current span.
     *
     * @param float|null $endTimestamp Takes an endTimestamp if the end should not be the time when you call this function
     *
     * @return EventId|null Finish for a span always returns null
     */
    public function finish(?float $endTimestamp = null): ?EventId
    {
        $this->endTimestamp = $endTimestamp ?? microtime(true);

        return null;
    }

    /**
     * Creates a new {@see Span} while setting the current ID as `parentSpanId`.
     * Also the `sampled` decision will be inherited.
     *
     * @param SpanContext $context The context of the child span
     */
    public function startChild(SpanContext $context): self
    {
        $context = clone $context;
        $context->setSampled($this->sampled);
        $context->setParentSpanId($this->spanId);
        $context->setTraceId($this->traceId);

        $span = new self($context);
        $span->transaction = $this->transaction;
        $span->spanRecorder = $this->spanRecorder;

        if ($span->spanRecorder !== null) {
            $span->spanRecorder->add($span);
        }

        return $span;
    }

    /**
     * Gets the span recorder attached to this span.
     *
     * @internal
     */
    public function getSpanRecorder(): ?SpanRecorder
    {
        return $this->spanRecorder;
    }

    /**
     * Detaches the span recorder from this instance.
     *
     * @return $this
     */
    public function detachSpanRecorder()
    {
        $this->spanRecorder = null;

        return $this;
    }

    /**
     * @deprecated Metrics are no longer supported. Metrics API is a no-op and will be removed in 5.x.
     */
    public function getMetricsSummary(): array
    {
        return [];
    }

    /**
     * @deprecated Metrics are no longer supported. Metrics API is a no-op and will be removed in 5.x.
     */
    public function setMetricsSummary(
        string $type,
        string $key,
        $value,
        Unit $unit,
        array $tags
    ): void {
    }

    /**
     * Sets the trace origin for this span.
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * Sets the trace origin of the span.
     *
     * @return $this
     */
    public function setOrigin(?string $origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Returns the transaction containing this span.
     */
    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    /**
     * Returns a string that can be used for the `sentry-trace` header & meta tag.
     */
    public function toTraceparent(): string
    {
        $sampled = '';

        if ($this->sampled !== null) {
            $sampled = $this->sampled ? '-1' : '-0';
        }

        return \sprintf('%s-%s%s', (string) $this->traceId, (string) $this->spanId, $sampled);
    }

    /**
     * Returns a string that can be used for the W3C `traceparent` header & meta tag.
     *
     * @deprecated since version 4.12. To be removed in version 5.0.
     */
    public function toW3CTraceparent(): string
    {
        return '';
    }

    /**
     * Returns a string that can be used for the `baggage` header & meta tag.
     */
    public function toBaggage(): string
    {
        $transaction = $this->getTransaction();

        if ($transaction !== null) {
            return (string) $transaction->getDynamicSamplingContext();
        }

        return '';
    }
}
