<?php

declare (strict_types=1);
namespace Sentry\State;

use Sentry\Breadcrumb;
use Sentry\CheckInStatus;
use Sentry\ClientInterface;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\EventId;
use Sentry\Integration\IntegrationInterface;
use Sentry\MonitorConfig;
use Sentry\SentrySdk;
use Sentry\Severity;
use Sentry\Tracing\Span;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;
/**
 * An implementation of {@see HubInterface} that uses {@see SentrySdk} internally
 * to manage the current hub.
 */
final class HubAdapter implements \Sentry\State\HubInterface
{
    /**
     * @var self|null The single instance which forwards all calls to {@see SentrySdk}
     */
    private static $instance;
    /**
     * Constructor.
     */
    private function __construct()
    {
    }
    /**
     * Gets the instance of this class. This is a singleton, so once initialized
     * you will always get the same instance.
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * {@inheritdoc}
     */
    public function getClient() : ?\Sentry\ClientInterface
    {
        return \Sentry\SentrySdk::getCurrentHub()->getClient();
    }
    /**
     * {@inheritdoc}
     */
    public function getLastEventId() : ?\Sentry\EventId
    {
        return \Sentry\SentrySdk::getCurrentHub()->getLastEventId();
    }
    /**
     * {@inheritdoc}
     */
    public function pushScope() : \Sentry\State\Scope
    {
        return \Sentry\SentrySdk::getCurrentHub()->pushScope();
    }
    /**
     * {@inheritdoc}
     */
    public function popScope() : bool
    {
        return \Sentry\SentrySdk::getCurrentHub()->popScope();
    }
    /**
     * {@inheritdoc}
     */
    public function withScope(callable $callback)
    {
        return \Sentry\SentrySdk::getCurrentHub()->withScope($callback);
    }
    /**
     * {@inheritdoc}
     */
    public function configureScope(callable $callback) : void
    {
        \Sentry\SentrySdk::getCurrentHub()->configureScope($callback);
    }
    /**
     * {@inheritdoc}
     */
    public function bindClient(\Sentry\ClientInterface $client) : void
    {
        \Sentry\SentrySdk::getCurrentHub()->bindClient($client);
    }
    /**
     * {@inheritdoc}
     */
    public function captureMessage(string $message, ?\Sentry\Severity $level = null, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        return \Sentry\SentrySdk::getCurrentHub()->captureMessage($message, $level, $hint);
    }
    /**
     * {@inheritdoc}
     */
    public function captureException(\Throwable $exception, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        return \Sentry\SentrySdk::getCurrentHub()->captureException($exception, $hint);
    }
    /**
     * {@inheritdoc}
     */
    public function captureEvent(\Sentry\Event $event, ?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        return \Sentry\SentrySdk::getCurrentHub()->captureEvent($event, $hint);
    }
    /**
     * {@inheritdoc}
     */
    public function captureLastError(?\Sentry\EventHint $hint = null) : ?\Sentry\EventId
    {
        return \Sentry\SentrySdk::getCurrentHub()->captureLastError($hint);
    }
    /**
     * {@inheritdoc}
     *
     * @param int|float|null $duration
     */
    public function captureCheckIn(string $slug, \Sentry\CheckInStatus $status, $duration = null, ?\Sentry\MonitorConfig $monitorConfig = null, ?string $checkInId = null) : ?string
    {
        return \Sentry\SentrySdk::getCurrentHub()->captureCheckIn($slug, $status, $duration, $monitorConfig, $checkInId);
    }
    /**
     * {@inheritdoc}
     */
    public function addBreadcrumb(\Sentry\Breadcrumb $breadcrumb) : bool
    {
        return \Sentry\SentrySdk::getCurrentHub()->addBreadcrumb($breadcrumb);
    }
    /**
     * {@inheritdoc}
     */
    public function getIntegration(string $className) : ?\Sentry\Integration\IntegrationInterface
    {
        return \Sentry\SentrySdk::getCurrentHub()->getIntegration($className);
    }
    /**
     * {@inheritdoc}
     */
    public function startTransaction(\Sentry\Tracing\TransactionContext $context, array $customSamplingContext = []) : \Sentry\Tracing\Transaction
    {
        return \Sentry\SentrySdk::getCurrentHub()->startTransaction($context, $customSamplingContext);
    }
    /**
     * {@inheritdoc}
     */
    public function getTransaction() : ?\Sentry\Tracing\Transaction
    {
        return \Sentry\SentrySdk::getCurrentHub()->getTransaction();
    }
    /**
     * {@inheritdoc}
     */
    public function getSpan() : ?\Sentry\Tracing\Span
    {
        return \Sentry\SentrySdk::getCurrentHub()->getSpan();
    }
    /**
     * {@inheritdoc}
     */
    public function setSpan(?\Sentry\Tracing\Span $span) : \Sentry\State\HubInterface
    {
        return \Sentry\SentrySdk::getCurrentHub()->setSpan($span);
    }
    /**
     * @see https://www.php.net/manual/en/language.oop5.cloning.php#object.clone
     */
    public function __clone()
    {
        throw new \BadMethodCallException('Cloning is forbidden.');
    }
    /**
     * @see https://www.php.net/manual/en/language.oop5.magic.php#object.wakeup
     */
    public function __wakeup()
    {
        throw new \BadMethodCallException('Unserializing instances of this class is forbidden.');
    }
    /**
     * @see https://www.php.net/manual/en/language.oop5.magic.php#object.sleep
     */
    public function __sleep()
    {
        throw new \BadMethodCallException('Serializing instances of this class is forbidden.');
    }
}
