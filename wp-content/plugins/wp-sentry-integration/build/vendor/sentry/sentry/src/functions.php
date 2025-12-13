<?php

declare (strict_types=1);
namespace Sentry;

use WPSentry\ScopedVendor\Psr\Log\LoggerInterface;
use Sentry\HttpClient\HttpClientInterface;
use Sentry\Integration\IntegrationInterface;
use Sentry\Logs\Logs;
use Sentry\Metrics\Metrics;
use Sentry\State\Scope;
use Sentry\Tracing\PropagationContext;
use Sentry\Tracing\SpanContext;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;
/**
 * Creates a new Client and Hub which will be set as current.
 *
 * @param array{
 *     attach_metric_code_locations?: bool,
 *     attach_stacktrace?: bool,
 *     before_breadcrumb?: callable,
 *     before_send?: callable,
 *     before_send_check_in?: callable,
 *     before_send_log?: callable,
 *     before_send_transaction?: callable,
 *     capture_silenced_errors?: bool,
 *     context_lines?: int|null,
 *     default_integrations?: bool,
 *     dsn?: string|bool|Dsn|null,
 *     enable_logs?: bool,
 *     environment?: string|null,
 *     error_types?: int|null,
 *     http_client?: HttpClientInterface|null,
 *     http_compression?: bool,
 *     http_connect_timeout?: int|float,
 *     http_proxy?: string|null,
 *     http_proxy_authentication?: string|null,
 *     http_ssl_verify_peer?: bool,
 *     http_timeout?: int|float,
 *     ignore_exceptions?: array<class-string>,
 *     ignore_transactions?: array<string>,
 *     in_app_exclude?: array<string>,
 *     in_app_include?: array<string>,
 *     integrations?: IntegrationInterface[]|callable(IntegrationInterface[]): IntegrationInterface[],
 *     logger?: LoggerInterface|null,
 *     max_breadcrumbs?: int,
 *     max_request_body_size?: "none"|"never"|"small"|"medium"|"always",
 *     max_value_length?: int,
 *     org_id?: int|null,
 *     prefixes?: array<string>,
 *     profiles_sample_rate?: int|float|null,
 *     release?: string|null,
 *     sample_rate?: float|int,
 *     send_attempts?: int,
 *     send_default_pii?: bool,
 *     server_name?: string,
 *     spotlight?: bool,
 *     spotlight_url?: string,
 *     strict_trace_propagation?: bool,
 *     tags?: array<string>,
 *     trace_propagation_targets?: array<string>|null,
 *     traces_sample_rate?: float|int|null,
 *     traces_sampler?: callable|null,
 *     transport?: callable,
 * } $options The client options
 */
function init(array $options = []) : void
{
    $client = \Sentry\ClientBuilder::create($options)->getClient();
    \Sentry\SentrySdk::init()->bindClient($client);
}
/**
 * Captures a message event and sends it to Sentry.
 *
 * @param string         $message The message
 * @param Severity|null  $level   The severity level of the message
 * @param EventHint|null $hint    Object that can contain additional information about the event
 */
function captureMessage(string $message, ?\Sentry\Severity $level = null, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
{
    return \Sentry\SentrySdk::getCurrentHub()->captureMessage($message, $level, $hint);
}
/**
 * Captures an exception event and sends it to Sentry.
 *
 * @param \Throwable     $exception The exception
 * @param EventHint|null $hint      Object that can contain additional information about the event
 */
function captureException(\Throwable $exception, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
{
    return \Sentry\SentrySdk::getCurrentHub()->captureException($exception, $hint);
}
/**
 * Captures a new event using the provided data.
 *
 * @param Event          $event The event being captured
 * @param EventHint|null $hint  May contain additional information about the event
 */
function captureEvent(\Sentry\Event $event, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
{
    return \Sentry\SentrySdk::getCurrentHub()->captureEvent($event, $hint);
}
/**
 * Logs the most recent error (obtained with {@see error_get_last()}).
 *
 * @param EventHint|null $hint Object that can contain additional information about the event
 */
function captureLastError(?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
{
    return \Sentry\SentrySdk::getCurrentHub()->captureLastError($hint);
}
/**
 * Captures a check-in and sends it to Sentry.
 *
 * @param string             $slug          Identifier of the Monitor
 * @param CheckInStatus      $status        The status of the check-in
 * @param int|float|null     $duration      The duration of the check-in
 * @param MonitorConfig|null $monitorConfig Configuration of the Monitor
 * @param string|null        $checkInId     A check-in ID from the previous check-in
 */
function captureCheckIn(string $slug, \Sentry\CheckInStatus $status, $duration = null, ?\Sentry\MonitorConfig $monitorConfig = null, ?string $checkInId = null) : ?string
{
    return \Sentry\SentrySdk::getCurrentHub()->captureCheckIn($slug, $status, $duration, $monitorConfig, $checkInId);
}
/**
 * Execute the given callable while wrapping it in a monitor check-in.
 *
 * @param string             $slug          Identifier of the Monitor
 * @param callable           $callback      The callable that is going to be monitored
 * @param MonitorConfig|null $monitorConfig Configuration of the Monitor
 *
 * @return mixed
 */
function withMonitor(string $slug, callable $callback, ?\Sentry\MonitorConfig $monitorConfig = null)
{
    $checkInId = \Sentry\SentrySdk::getCurrentHub()->captureCheckIn($slug, \Sentry\CheckInStatus::inProgress(), null, $monitorConfig);
    $status = \Sentry\CheckInStatus::ok();
    $duration = 0;
    try {
        $start = \microtime(\true);
        $result = $callback();
        $duration = \microtime(\true) - $start;
        return $result;
    } catch (\Throwable $e) {
        $status = \Sentry\CheckInStatus::error();
        throw $e;
    } finally {
        \Sentry\SentrySdk::getCurrentHub()->captureCheckIn($slug, $status, $duration, $monitorConfig, $checkInId);
    }
}
/**
 * Records a new breadcrumb which will be attached to future events. They
 * will be added to subsequent events to provide more context on user's
 * actions prior to an error or crash.
 *
 * @param Breadcrumb|string    $category  The category of the breadcrumb, can be a Breadcrumb instance as well (in which case the other parameters are ignored)
 * @param string|null          $message   Breadcrumb message
 * @param array<string, mixed> $metadata  Additional information about the breadcrumb
 * @param string               $level     The error level of the breadcrumb
 * @param string               $type      The type of the breadcrumb
 * @param float|null           $timestamp Optional timestamp of the breadcrumb
 */
function addBreadcrumb($category, ?string $message = null, array $metadata = [], string $level = \Sentry\Breadcrumb::LEVEL_INFO, string $type = \Sentry\Breadcrumb::TYPE_DEFAULT, ?float $timestamp = null) : void
{
    \Sentry\SentrySdk::getCurrentHub()->addBreadcrumb($category instanceof \Sentry\Breadcrumb ? $category : new \Sentry\Breadcrumb($level, $type, $category, $message, $metadata, $timestamp));
}
/**
 * Calls the given callback passing to it the current scope so that any
 * operation can be run within its context.
 *
 * @param callable $callback The callback to be executed
 */
function configureScope(callable $callback) : void
{
    \Sentry\SentrySdk::getCurrentHub()->configureScope($callback);
}
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
function withScope(callable $callback)
{
    return \Sentry\SentrySdk::getCurrentHub()->withScope($callback);
}
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
 * @param TransactionContext   $context               Properties of the new transaction
 * @param array<string, mixed> $customSamplingContext Additional context that will be passed to the {@see Tracing\SamplingContext}
 */
function startTransaction(\Sentry\Tracing\TransactionContext $context, array $customSamplingContext = []) : \Sentry\Tracing\Transaction
{
    return \Sentry\SentrySdk::getCurrentHub()->startTransaction($context, $customSamplingContext);
}
/**
 * Execute the given callable while wrapping it in a span added as a child to the current transaction and active span.
 * If there is no transaction active this is a no-op and the scope passed to the trace callable will be unused.
 *
 * @template T
 *
 * @param callable(Scope): T $trace   The callable that is going to be traced
 * @param SpanContext        $context The context of the span to be created
 *
 * @return T
 */
function trace(callable $trace, \Sentry\Tracing\SpanContext $context)
{
    return \Sentry\SentrySdk::getCurrentHub()->withScope(function (\Sentry\State\Scope $scope) use($context, $trace) {
        $parentSpan = $scope->getSpan();
        // If there is a span set on the scope and it's sampled there is an active transaction.
        // If that is the case we create the child span and set it on the scope.
        // Otherwise we only execute the callable without creating a span.
        if ($parentSpan !== null && $parentSpan->getSampled()) {
            $span = $parentSpan->startChild($context);
            $scope->setSpan($span);
        }
        try {
            return $trace($scope);
        } finally {
            if (isset($span)) {
                $span->finish();
                $scope->setSpan($parentSpan);
            }
        }
    });
}
/**
 * Creates the current Sentry traceparent string, to be used as a HTTP header value
 * or HTML meta tag value.
 * This function is context aware, as in it either returns the traceparent based
 * on the current span, or the scope's propagation context.
 */
function getTraceparent() : string
{
    $hub = \Sentry\SentrySdk::getCurrentHub();
    $client = $hub->getClient();
    if ($client !== null) {
        $options = $client->getOptions();
        if ($options !== null && $options->isTracingEnabled()) {
            $span = \Sentry\SentrySdk::getCurrentHub()->getSpan();
            if ($span !== null) {
                return $span->toTraceparent();
            }
        }
    }
    $traceParent = '';
    $hub->configureScope(function (\Sentry\State\Scope $scope) use(&$traceParent) {
        $traceParent = $scope->getPropagationContext()->toTraceparent();
    });
    return $traceParent;
}
/**
 * Creates the current W3C traceparent string, to be used as a HTTP header value
 * or HTML meta tag value.
 * This function is context aware, as in it either returns the traceparent based
 * on the current span, or the scope's propagation context.
 *
 * @deprecated since version 4.12. To be removed in version 5.0.
 */
function getW3CTraceparent() : string
{
    return '';
}
/**
 * Creates the baggage content string, to be used as a HTTP header value
 * or HTML meta tag value.
 * This function is context aware, as in it either returns the baggage based
 * on the current span or the scope's propagation context.
 */
function getBaggage() : string
{
    $hub = \Sentry\SentrySdk::getCurrentHub();
    $client = $hub->getClient();
    if ($client !== null) {
        $options = $client->getOptions();
        if ($options !== null && $options->isTracingEnabled()) {
            $span = \Sentry\SentrySdk::getCurrentHub()->getSpan();
            if ($span !== null) {
                return $span->toBaggage();
            }
        }
    }
    $baggage = '';
    $hub->configureScope(function (\Sentry\State\Scope $scope) use(&$baggage) {
        $baggage = $scope->getPropagationContext()->toBaggage();
    });
    return $baggage;
}
/**
 * Continue a trace based on HTTP header values.
 * If the SDK is configured with enabled tracing,
 * this function returns a populated TransactionContext.
 * In any other cases, it populates the propagation context on the scope.
 */
function continueTrace(string $sentryTrace, string $baggage) : \Sentry\Tracing\TransactionContext
{
    $hub = \Sentry\SentrySdk::getCurrentHub();
    $hub->configureScope(function (\Sentry\State\Scope $scope) use($sentryTrace, $baggage) {
        $propagationContext = \Sentry\Tracing\PropagationContext::fromHeaders($sentryTrace, $baggage);
        $scope->setPropagationContext($propagationContext);
    });
    return \Sentry\Tracing\TransactionContext::fromHeaders($sentryTrace, $baggage);
}
/**
 * Get the Sentry Logs client.
 */
function logger() : \Sentry\Logs\Logs
{
    return \Sentry\Logs\Logs::getInstance();
}
/**
 * @deprecated Metrics are no longer supported. Metrics API is a no-op and will be removed in 5.x.
 */
function metrics() : \Sentry\Metrics\Metrics
{
    return \Sentry\Metrics\Metrics::getInstance();
}
/**
 * Adds a feature flag evaluation to the current scope.
 * When invoked repeatedly for the same name, the most recent value is used.
 */
function addFeatureFlag(string $name, bool $result) : void
{
    \Sentry\SentrySdk::getCurrentHub()->configureScope(function (\Sentry\State\Scope $scope) use($name, $result) {
        $scope->addFeatureFlag($name, $result);
    });
}
