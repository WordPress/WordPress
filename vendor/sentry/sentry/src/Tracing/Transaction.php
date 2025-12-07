<?php

declare(strict_types=1);

namespace Sentry\Tracing;

use Sentry\Event;
use Sentry\EventId;
use Sentry\Profiling\Profiler;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;

/**
 * This class stores all the information about a Transaction.
 */
final class Transaction extends Span
{
    /**
     * @var HubInterface The hub instance
     */
    private $hub;

    /**
     * @var string Name of the transaction
     */
    private $name;

    /**
     * @var Transaction The transaction
     */
    protected $transaction;

    /**
     * @var TransactionMetadata
     */
    protected $metadata;

    /**
     * @var Profiler|null Reference instance to the {@see Profiler}
     */
    protected $profiler;

    /**
     * Span constructor.
     *
     * @param TransactionContext $context The context to create the transaction with
     * @param HubInterface|null  $hub     Instance of a hub to flush the transaction
     *
     * @internal
     */
    public function __construct(TransactionContext $context, ?HubInterface $hub = null)
    {
        parent::__construct($context);

        $this->hub = $hub ?? SentrySdk::getCurrentHub();
        $this->name = $context->getName();
        $this->metadata = $context->getMetadata();
        $this->transaction = $this;
    }

    /**
     * Gets the name of this transaction.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this transaction.
     *
     * @param string $name The name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * Gets the transaction dynamic sampling context.
     */
    public function getDynamicSamplingContext(): DynamicSamplingContext
    {
        if ($this->metadata->getDynamicSamplingContext() !== null) {
            return $this->metadata->getDynamicSamplingContext();
        }

        $samplingContext = DynamicSamplingContext::fromTransaction($this->transaction, $this->hub);
        $this->getMetadata()->setDynamicSamplingContext($samplingContext);

        return $samplingContext;
    }

    /**
     * Attaches a {@see SpanRecorder} to the transaction itself.
     *
     * @param int $maxSpans The maximum number of spans that can be recorded
     */
    public function initSpanRecorder(int $maxSpans = 1000): self
    {
        if ($this->spanRecorder === null) {
            $this->spanRecorder = new SpanRecorder($maxSpans);
        }

        $this->spanRecorder->add($this);

        return $this;
    }

    public function initProfiler(): Profiler
    {
        if ($this->profiler === null) {
            $client = $this->hub->getClient();
            $options = $client !== null ? $client->getOptions() : null;

            $this->profiler = new Profiler($options);
        }

        return $this->profiler;
    }

    public function getProfiler(): ?Profiler
    {
        return $this->profiler;
    }

    public function detachProfiler(): self
    {
        $this->profiler = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function finish(?float $endTimestamp = null): ?EventId
    {
        if ($this->profiler !== null) {
            $this->profiler->stop();
        }

        if ($this->endTimestamp !== null) {
            // Transaction was already finished once and we don't want to re-flush it
            return null;
        }

        parent::finish($endTimestamp);

        if ($this->sampled !== true) {
            return null;
        }

        $finishedSpans = [];

        if ($this->spanRecorder !== null) {
            foreach ($this->spanRecorder->getSpans() as $span) {
                if ($span->getSpanId() !== $this->getSpanId() && $span->getEndTimestamp() !== null) {
                    $finishedSpans[] = $span;
                }
            }
        }

        $event = Event::createTransaction();
        $event->setSpans($finishedSpans);
        $event->setStartTimestamp($this->startTimestamp);
        $event->setTimestamp($this->endTimestamp);
        $event->setTags($this->tags);
        $event->setTransaction($this->name);
        $event->setContext('trace', $this->getTraceContext());
        $event->setSdkMetadata('dynamic_sampling_context', $this->getDynamicSamplingContext());
        $event->setSdkMetadata('transaction_metadata', $this->getMetadata());

        if ($this->profiler !== null) {
            $profile = $this->profiler->getProfile();
            if ($profile !== null) {
                $event->setSdkMetadata('profile', $profile);
            }
        }

        return $this->hub->captureEvent($event);
    }
}
