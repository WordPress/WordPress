<?php

declare(strict_types=1);

namespace Sentry\Tracing;

use Sentry\Tracing\Traits\TraceHeaderParserTrait;

final class TransactionContext extends SpanContext
{
    use TraceHeaderParserTrait;

    public const DEFAULT_NAME = '<unlabeled transaction>';

    /**
     * @var string Name of the transaction
     */
    private $name;

    /**
     * @var bool|null The parent's sampling decision
     */
    private $parentSampled;

    /**
     * @var TransactionMetadata The transaction metadata
     */
    private $metadata;

    /**
     * Constructor.
     *
     * @param string                   $name          The name of the transaction
     * @param bool|null                $parentSampled The parent's sampling decision
     * @param TransactionMetadata|null $metadata      The transaction metadata
     */
    public function __construct(
        string $name = self::DEFAULT_NAME,
        ?bool $parentSampled = null,
        ?TransactionMetadata $metadata = null
    ) {
        $this->name = $name;
        $this->parentSampled = $parentSampled;
        $this->metadata = $metadata ?? new TransactionMetadata();
    }

    /**
     * @return self
     */
    public static function make()
    {
        return new self();
    }

    /**
     * Gets the name of the transaction.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the transaction.
     *
     * @param string $name The name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the parent's sampling decision.
     */
    public function getParentSampled(): ?bool
    {
        return $this->parentSampled;
    }

    /**
     * Sets the parent's sampling decision.
     *
     * @param bool|null $parentSampled The decision
     */
    public function setParentSampled(?bool $parentSampled): self
    {
        $this->parentSampled = $parentSampled;

        return $this;
    }

    /**
     * Gets the transaction metadata.
     */
    public function getMetadata(): TransactionMetadata
    {
        return $this->metadata;
    }

    /**
     * Sets the transaction metadata.
     *
     * @param TransactionMetadata $metadata The transaction metadata
     */
    public function setMetadata(TransactionMetadata $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Sets the transaction source.
     *
     * @param TransactionSource $transactionSource The transaction source
     */
    public function setSource(TransactionSource $transactionSource): self
    {
        $this->metadata->setSource($transactionSource);

        return $this;
    }

    /**
     * Returns a context populated with the data of the given environment variables.
     *
     * @param string $sentryTrace The sentry-trace value from the environment
     * @param string $baggage     The baggage header value from the environment
     */
    public static function fromEnvironment(string $sentryTrace, string $baggage): self
    {
        return self::parseTraceAndBaggage($sentryTrace, $baggage);
    }

    /**
     * Returns a context populated with the data of the given headers.
     *
     * @param string $sentryTraceHeader The sentry-trace header from an incoming request
     * @param string $baggageHeader     The baggage header from an incoming request
     */
    public static function fromHeaders(string $sentryTraceHeader, string $baggageHeader): self
    {
        return self::parseTraceAndBaggage($sentryTraceHeader, $baggageHeader);
    }

    private static function parseTraceAndBaggage(string $sentryTrace, string $baggage): self
    {
        $context = new self();
        $parsedData = self::parseTraceAndBaggageHeaders($sentryTrace, $baggage);

        if ($parsedData['traceId'] !== null) {
            $context->traceId = $parsedData['traceId'];
        }

        if ($parsedData['parentSpanId'] !== null) {
            $context->parentSpanId = $parsedData['parentSpanId'];
        }

        if ($parsedData['parentSampled'] !== null) {
            $context->parentSampled = $parsedData['parentSampled'];
        }

        if ($parsedData['dynamicSamplingContext'] !== null) {
            $context->getMetadata()->setDynamicSamplingContext($parsedData['dynamicSamplingContext']);
        }

        if ($parsedData['parentSamplingRate'] !== null) {
            $context->getMetadata()->setParentSamplingRate($parsedData['parentSamplingRate']);
        }

        if ($parsedData['sampleRand'] !== null) {
            $context->getMetadata()->setSampleRand($parsedData['sampleRand']);
        }

        return $context;
    }
}
