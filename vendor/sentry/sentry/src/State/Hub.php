<?php

declare(strict_types=1);

namespace Sentry\State;

use Psr\Log\NullLogger;
use Sentry\Breadcrumb;
use Sentry\CheckIn;
use Sentry\CheckInStatus;
use Sentry\ClientInterface;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\EventId;
use Sentry\Integration\IntegrationInterface;
use Sentry\MonitorConfig;
use Sentry\Severity;
use Sentry\Tracing\SamplingContext;
use Sentry\Tracing\Span;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;

/**
 * This class is a basic implementation of the {@see HubInterface} interface.
 */
class Hub implements HubInterface
{
    /**
     * @var Layer[] The stack of client/scope pairs
     */
    private $stack = [];

    /**
     * @var EventId|null The ID of the last captured event
     */
    private $lastEventId;

    /**
     * Hub constructor.
     *
     * @param ClientInterface|null $client The client bound to the hub
     * @param Scope|null           $scope  The scope bound to the hub
     */
    public function __construct(?ClientInterface $client = null, ?Scope $scope = null)
    {
        $this->stack[] = new Layer($client, $scope ?? new Scope());
    }

    /**
     * {@inheritdoc}
     */
    public function getClient(): ?ClientInterface
    {
        return $this->getStackTop()->getClient();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastEventId(): ?EventId
    {
        return $this->lastEventId;
    }

    /**
     * {@inheritdoc}
     */
    public function pushScope(): Scope
    {
        $clonedScope = clone $this->getScope();

        $this->stack[] = new Layer($this->getClient(), $clonedScope);

        return $clonedScope;
    }

    /**
     * {@inheritdoc}
     */
    public function popScope(): bool
    {
        if (\count($this->stack) === 1) {
            return false;
        }

        return array_pop($this->stack) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function withScope(callable $callback)
    {
        $scope = $this->pushScope();

        try {
            return $callback($scope);
        } finally {
            $this->popScope();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureScope(callable $callback): void
    {
        $callback($this->getScope());
    }

    /**
     * {@inheritdoc}
     */
    public function bindClient(ClientInterface $client): void
    {
        $layer = $this->getStackTop();
        $layer->setClient($client);
    }

    /**
     * {@inheritdoc}
     */
    public function captureMessage(string $message, ?Severity $level = null, ?EventHint $hint = null): ?EventId
    {
        $client = $this->getClient();

        if ($client !== null) {
            return $this->lastEventId = $client->captureMessage($message, $level, $this->getScope(), $hint);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function captureException(\Throwable $exception, ?EventHint $hint = null): ?EventId
    {
        $client = $this->getClient();

        if ($client !== null) {
            return $this->lastEventId = $client->captureException($exception, $this->getScope(), $hint);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function captureEvent(Event $event, ?EventHint $hint = null): ?EventId
    {
        $client = $this->getClient();

        if ($client !== null) {
            return $this->lastEventId = $client->captureEvent($event, $hint, $this->getScope());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function captureLastError(?EventHint $hint = null): ?EventId
    {
        $client = $this->getClient();

        if ($client !== null) {
            return $this->lastEventId = $client->captureLastError($this->getScope(), $hint);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param int|float|null $duration
     */
    public function captureCheckIn(string $slug, CheckInStatus $status, $duration = null, ?MonitorConfig $monitorConfig = null, ?string $checkInId = null): ?string
    {
        $client = $this->getClient();

        if ($client === null) {
            return null;
        }

        $options = $client->getOptions();
        $event = Event::createCheckIn();
        $checkIn = new CheckIn(
            $slug,
            $status,
            $checkInId,
            $options->getRelease(),
            $options->getEnvironment(),
            $duration,
            $monitorConfig
        );
        $event->setCheckIn($checkIn);
        $this->captureEvent($event);

        return $checkIn->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb): bool
    {
        $client = $this->getClient();

        if ($client === null) {
            return false;
        }

        $options = $client->getOptions();
        $beforeBreadcrumbCallback = $options->getBeforeBreadcrumbCallback();
        $maxBreadcrumbs = $options->getMaxBreadcrumbs();

        if ($maxBreadcrumbs <= 0) {
            return false;
        }

        $breadcrumb = $beforeBreadcrumbCallback($breadcrumb);

        if ($breadcrumb !== null) {
            $this->getScope()->addBreadcrumb($breadcrumb, $maxBreadcrumbs);
        }

        return $breadcrumb !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegration(string $className): ?IntegrationInterface
    {
        $client = $this->getClient();

        if ($client !== null) {
            return $client->getIntegration($className);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $customSamplingContext Additional context that will be passed to the {@see SamplingContext}
     */
    public function startTransaction(TransactionContext $context, array $customSamplingContext = []): Transaction
    {
        $transaction = new Transaction($context, $this);
        $client = $this->getClient();
        $options = $client !== null ? $client->getOptions() : null;
        $logger = $options !== null ? $options->getLoggerOrNullLogger() : new NullLogger();

        if ($options === null || !$options->isTracingEnabled()) {
            $transaction->setSampled(false);

            $logger->warning(\sprintf('Transaction [%s] was started but tracing is not enabled.', (string) $transaction->getTraceId()), ['context' => $context]);

            return $transaction;
        }

        $samplingContext = SamplingContext::getDefault($context);
        $samplingContext->setAdditionalContext($customSamplingContext);

        $sampleSource = 'context';
        $sampleRand = $context->getMetadata()->getSampleRand();

        if ($transaction->getSampled() === null) {
            $tracesSampler = $options->getTracesSampler();

            if ($tracesSampler !== null) {
                $sampleRate = $tracesSampler($samplingContext);
                $sampleSource = 'config:traces_sampler';
            } else {
                $parentSampleRate = $context->getMetadata()->getParentSamplingRate();
                if ($parentSampleRate !== null) {
                    $sampleRate = $parentSampleRate;
                    $sampleSource = 'parent:sample_rate';
                } else {
                    $sampleRate = $this->getSampleRate(
                        $samplingContext->getParentSampled(),
                        $options->getTracesSampleRate() ?? 0
                    );
                    $sampleSource = $samplingContext->getParentSampled() !== null ? 'parent:sampling_decision' : 'config:traces_sample_rate';
                }
            }

            if (!$this->isValidSampleRate($sampleRate)) {
                $transaction->setSampled(false);

                $logger->warning(\sprintf('Transaction [%s] was started but not sampled because sample rate (decided by %s) is invalid.', (string) $transaction->getTraceId(), $sampleSource), ['context' => $context]);

                return $transaction;
            }

            $transaction->getMetadata()->setSamplingRate($sampleRate);

            // Always overwrite the sample_rate in the DSC
            $dynamicSamplingContext = $context->getMetadata()->getDynamicSamplingContext();
            if ($dynamicSamplingContext !== null) {
                $dynamicSamplingContext->set('sample_rate', (string) $sampleRate, true);
            }

            if ($sampleRate === 0.0) {
                $transaction->setSampled(false);

                $logger->info(\sprintf('Transaction [%s] was started but not sampled because sample rate (decided by %s) is %s.', (string) $transaction->getTraceId(), $sampleSource, $sampleRate), ['context' => $context]);

                return $transaction;
            }

            $transaction->setSampled($sampleRand < $sampleRate);
        }

        if (!$transaction->getSampled()) {
            $logger->info(\sprintf('Transaction [%s] was started but not sampled, decided by %s.', (string) $transaction->getTraceId(), $sampleSource), ['context' => $context]);

            return $transaction;
        }

        $logger->info(\sprintf('Transaction [%s] was started and sampled, decided by %s.', (string) $transaction->getTraceId(), $sampleSource), ['context' => $context]);

        $transaction->initSpanRecorder();

        $profilesSampleRate = $options->getProfilesSampleRate();
        if ($profilesSampleRate === null) {
            $logger->info(\sprintf('Transaction [%s] is not profiling because `profiles_sample_rate` option is not set.', (string) $transaction->getTraceId()));
        } elseif ($this->sample($profilesSampleRate)) {
            $logger->info(\sprintf('Transaction [%s] started profiling because it was sampled.', (string) $transaction->getTraceId()));

            $transaction->initProfiler()->start();
        } else {
            $logger->info(\sprintf('Transaction [%s] is not profiling because it was not sampled.', (string) $transaction->getTraceId()));
        }

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransaction(): ?Transaction
    {
        return $this->getScope()->getTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function setSpan(?Span $span): HubInterface
    {
        $this->getScope()->setSpan($span);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpan(): ?Span
    {
        return $this->getScope()->getSpan();
    }

    /**
     * Gets the scope bound to the top of the stack.
     */
    private function getScope(): Scope
    {
        return $this->getStackTop()->getScope();
    }

    /**
     * Gets the topmost client/layer pair in the stack.
     */
    private function getStackTop(): Layer
    {
        return $this->stack[\count($this->stack) - 1];
    }

    private function getSampleRate(?bool $hasParentBeenSampled, float $fallbackSampleRate): float
    {
        if ($hasParentBeenSampled === true) {
            return 1.0;
        }

        if ($hasParentBeenSampled === false) {
            return 0.0;
        }

        return $fallbackSampleRate;
    }

    /**
     * @param mixed $sampleRate
     */
    private function sample($sampleRate): bool
    {
        if ($sampleRate === 0.0 || $sampleRate === null) {
            return false;
        }

        if ($sampleRate === 1.0) {
            return true;
        }

        return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax() < $sampleRate;
    }

    /**
     * @param mixed $sampleRate
     */
    private function isValidSampleRate($sampleRate): bool
    {
        if (!\is_float($sampleRate) && !\is_int($sampleRate)) {
            return false;
        }

        if ($sampleRate < 0 || $sampleRate > 1) {
            return false;
        }

        return true;
    }
}
