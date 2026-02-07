<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Integration\IntegrationInterface;
use Sentry\State\Scope;
use Sentry\Transport\Result;

interface ClientInterface
{
    /**
     * Returns the options of the client.
     */
    public function getOptions(): Options;

    /**
     * Returns an URL for security policy reporting that's generated from the configured DSN.
     */
    public function getCspReportUrl(): ?string;

    /**
     * Logs a message.
     *
     * @param string         $message The message (primary description) for the event
     * @param Severity|null  $level   The level of the message to be sent
     * @param Scope|null     $scope   An optional scope keeping the state
     * @param EventHint|null $hint    Object that can contain additional information about the event
     */
    public function captureMessage(string $message, ?Severity $level = null, ?Scope $scope = null, ?EventHint $hint = null): ?EventId;

    /**
     * Logs an exception.
     *
     * @param \Throwable     $exception The exception object
     * @param Scope|null     $scope     An optional scope keeping the state
     * @param EventHint|null $hint      Object that can contain additional information about the event
     */
    public function captureException(\Throwable $exception, ?Scope $scope = null, ?EventHint $hint = null): ?EventId;

    /**
     * Logs the most recent error (obtained with {@link error_get_last}).
     *
     * @param Scope|null     $scope An optional scope keeping the state
     * @param EventHint|null $hint  Object that can contain additional information about the event
     */
    public function captureLastError(?Scope $scope = null, ?EventHint $hint = null): ?EventId;

    /**
     * Captures a new event using the provided data.
     *
     * @param Event          $event The event being captured
     * @param EventHint|null $hint  May contain additional information about the event
     * @param Scope|null     $scope An optional scope keeping the state
     */
    public function captureEvent(Event $event, ?EventHint $hint = null, ?Scope $scope = null): ?EventId;

    /**
     * Returns the integration instance if it is installed on the client.
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
     * Flushes the queue of events pending to be sent. If a timeout is provided
     * and the queue takes longer to drain, the promise resolves with `false`.
     *
     * @param int|null $timeout Maximum time in seconds the client should wait
     */
    public function flush(?int $timeout = null): Result;

    /**
     * Returns the stacktrace builder of the client.
     */
    public function getStacktraceBuilder(): StacktraceBuilder;
}
