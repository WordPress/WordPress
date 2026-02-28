<?php

declare(strict_types=1);

namespace Sentry\Tracing;

class SpanContext
{
    /**
     * @var string|null Description of the Span
     */
    private $description;

    /**
     * @var string|null Operation of the Span
     */
    private $op;

    /**
     * @var SpanStatus|null Completion status of the Span
     */
    private $status;

    /**
     * @var SpanId|null ID of the parent Span
     */
    protected $parentSpanId;

    /**
     * @var bool|null Has the sample decision been made?
     */
    private $sampled;

    /**
     * @var SpanId|null Span ID
     */
    private $spanId;

    /**
     * @var TraceId|null Trace ID
     */
    protected $traceId;

    /**
     * @var array<string, string> A List of tags associated to this Span
     */
    private $tags = [];

    /**
     * @var array<string, mixed> An arbitrary mapping of additional metadata
     */
    private $data = [];

    /**
     * @var float|null Timestamp in seconds (epoch time) indicating when the span started
     */
    private $startTimestamp;

    /**
     * @var float|null Timestamp in seconds (epoch time) indicating when the span ended
     */
    private $endTimestamp;

    /**
     * @var string|null the trace origin of the span
     */
    private $origin;

    /**
     * @return self
     */
    public static function make()
    {
        return new self();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return $this
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getOp(): ?string
    {
        return $this->op;
    }

    /**
     * @return $this
     */
    public function setOp(?string $op)
    {
        $this->op = $op;

        return $this;
    }

    public function getStatus(): ?SpanStatus
    {
        return $this->status;
    }

    /**
     * @return $this
     */
    public function setStatus(?SpanStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getParentSpanId(): ?SpanId
    {
        return $this->parentSpanId;
    }

    /**
     * @return $this
     */
    public function setParentSpanId(?SpanId $parentSpanId)
    {
        $this->parentSpanId = $parentSpanId;

        return $this;
    }

    public function getSampled(): ?bool
    {
        return $this->sampled;
    }

    /**
     * @return $this
     */
    public function setSampled(?bool $sampled)
    {
        $this->sampled = $sampled;

        return $this;
    }

    public function getSpanId(): ?SpanId
    {
        return $this->spanId;
    }

    /**
     * @return $this
     */
    public function setSpanId(?SpanId $spanId)
    {
        $this->spanId = $spanId;

        return $this;
    }

    public function getTraceId(): ?TraceId
    {
        return $this->traceId;
    }

    /**
     * @return $this
     */
    public function setTraceId(?TraceId $traceId)
    {
        $this->traceId = $traceId;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array<string, string> $tags
     *
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getStartTimestamp(): ?float
    {
        return $this->startTimestamp;
    }

    /**
     * @return $this
     */
    public function setStartTimestamp(?float $startTimestamp)
    {
        $this->startTimestamp = $startTimestamp;

        return $this;
    }

    public function getEndTimestamp(): ?float
    {
        return $this->endTimestamp;
    }

    /**
     * @return $this
     */
    public function setEndTimestamp(?float $endTimestamp)
    {
        $this->endTimestamp = $endTimestamp;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * @return $this
     */
    public function setOrigin(?string $origin)
    {
        $this->origin = $origin;

        return $this;
    }
}
