<?php

namespace YoastSEO_Vendor\Psr\Log;

/**
 * This is a simple Logger implementation that other Loggers can inherit from.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 */
abstract class AbstractLogger implements \YoastSEO_Vendor\Psr\Log\LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::EMERGENCY, $message, $context);
    }
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::ALERT, $message, $context);
    }
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::CRITICAL, $message, $context);
    }
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::ERROR, $message, $context);
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::WARNING, $message, $context);
    }
    /**
     * Normal but significant events.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::NOTICE, $message, $context);
    }
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::INFO, $message, $context);
    }
    /**
     * Detailed debug information.
     *
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        $this->log(\YoastSEO_Vendor\Psr\Log\LogLevel::DEBUG, $message, $context);
    }
}
