<?php

declare(strict_types=1);

namespace Sentry\State;

use Sentry\Breadcrumb;
use Sentry\CheckInStatus;
use Sentry\ClientInterface;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\EventId;
use Sentry\Integration\IntegrationInterface;
use Sentry\MonitorConfig;
use Sentry\Severity;
use Sentry\Tracing\Span;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;

interface HubInterface
{
    /**
     * Gets the client bound to the top of the stack.
     */
    public function getClient(): ?ClientInterface;

    /**
     * Gets the ID of the last captured event.
     */
    public function getLastEventId(): ?EventId;

    /**
     * Creates a new scope to store context information that will be layered on
     * top of the current one. It is isolated, i.e. all breadcrumbs and context
     * information added to this scope will be removed once the scope ends. Be
     * sure to always remove this scope with {@see Hub::popScope} when the
     * operation finishes or throws.
     */
    public function pushScope(): Scope;

    /**
     * Removes a previously pushed scope from the stack. This restores the state
     * before the scope was pushed. All breadcrumbs and context information added
     * since the last call to {@see Hub::pushScope} are discarded.
     */
    public function popScope(): bool;

    /**
     * Creates a new scope with and executes the given operation within. The scope
     * is automatically removed once the operation finishes or throws.
     *
     * @param callable $callback The callback to be executed
     *
     * @psalm-template T
     *
     * @psalm-param callable(Scope): T $callback
     *
     * @return mixed|void The callback's return value, upon successful execution
     *
     * @psalm-return T
     */
    public function withScope(callable $callback);

    /**
     * Calls the given callback passing to it the current scope so that any
     * operation can be run within its context.
     */
    public function configureScope(callable $callback): void;

    /**
     * Binds the given client to the current scope.
     */
    public function bindClient(ClientInterface $client): void;

    /**
     * Captures a message event and sends it to Sentry.
     */
    public function captureMessage(string $message, ?Severity $level = null, ?EventHint $hint = null): ?EventId;

    /**
     * Captures an exception event and sends it to Sentry.
     */
    public function captureException(\Throwable $exception, ?EventHint $hint = null): ?EventId;

    /**
     * Captures a new event using the provided data.
     */
    public function captureEvent(Event $event, ?EventHint $hint = null): ?EventId;

    /**
     * Captures an event that logs the last occurred error.
     */
    public function captureLastError(?EventHint $hint = null): ?EventId;

    /**
     * Records a new breadcrumb which will be attached to future events. They
     * will be added to subsequent events to provide more context on user's
     * actions prior to an error or crash.
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb): bool;

    /**
     * Captures a check-in.
     *
     * @param int|float|null $duration
     */
    public function captureCheckIn(string $slug, CheckInStatus $status, $duration = null, ?MonitorConfig $monitorConfig = null, ?string $checkInId = null): ?string;

    /**
     * Gets the integration whose FQCN matches the given one if it's available on the current client.
     *
     * @param string $className The FQCN of the integration
     *
     * @psalm-template T of IntegrationInterface
     *
     * @psalm-param class-string<T> $className
     *
     * @psalm-return T|null
     */
    public function getIntegration(string $className): ?IntegrationInterface;

    /**
     * Starts a new `Transaction` and returns it. This is the entry point to manual
     * tracing instrumentation.
     *
     * A tree structure can be built by adding child spans to the transaction, and
     * child spans to other spans. To start a new child span within the transaction
     * or any span, call the respective `startChild()` method.
     *
     * Every child span must be finished before the transaction is finished,
     * otherwise the unfinished spans are discarded.
     *
     * The transaction must be finished with a call to its `finish()` method, at
     * which point the transaction with all its finished child spans will be sent to
     * Sentry.
     *
     * @param array<string, mixed> $customSamplingContext Additional context that will be passed to the {@see SamplingContext}
     */
    public function startTransaction(TransactionContext $context, array $customSamplingContext = []): Transaction;

    /**
     * Returns the transaction that is on the Hub.
     */
    public function getTransaction(): ?Transaction;

    /**
     * Returns the span that is on the Hub.
     */
    public function getSpan(): ?Span;

    /**
     * Sets the span on the Hub.
     */
    public function setSpan(?Span $span): HubInterface;
}
