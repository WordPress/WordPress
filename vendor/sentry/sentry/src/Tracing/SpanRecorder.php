<?php

declare(strict_types=1);

namespace Sentry\Tracing;

final class SpanRecorder
{
    /**
     * @var int Maximum number of spans that should be stored
     */
    private $maxSpans;

    /**
     * @var Span[] List of spans managed by this recorder
     */
    private $spans = [];

    /**
     * Constructor.
     *
     * @param int $maxSpans The maximum number of spans to record before
     *                      detaching the recorder from the span
     */
    public function __construct(int $maxSpans = 1000)
    {
        $this->maxSpans = $maxSpans;
    }

    /**
     * Adds a span to the list of recorded spans or detaches the recorder if the
     * maximum number of spans to store has been reached.
     */
    public function add(Span $span): self
    {
        if (\count($this->spans) > $this->maxSpans) {
            $span->detachSpanRecorder();
        } else {
            $this->spans[] = $span;
        }

        return $this;
    }

    /**
     * Gets all the spans managed by this recorder.
     *
     * @return Span[]
     */
    public function getSpans(): array
    {
        return $this->spans;
    }
}
