<?php

declare (strict_types=1);
namespace Sentry\Monolog;

use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Sentry\Severity;
if (\Monolog\Logger::API >= 3) {
    /**
     * Logic which is used if monolog >= 3 is installed.
     *
     * @internal
     */
    trait CompatibilityProcessingHandlerTrait
    {
        /**
         * @param array<string, mixed>|LogRecord $record
         */
        protected abstract function doWrite($record) : void;
        /**
         * {@inheritdoc}
         */
        protected function write(\Monolog\LogRecord $record) : void
        {
            $this->doWrite($record);
        }
        /**
         * Translates the Monolog level into the Sentry severity.
         */
        private static function getSeverityFromLevel(int $level) : \Sentry\Severity
        {
            $level = \Monolog\Level::from($level);
            switch ($level) {
                case \Monolog\Level::Debug:
                    return \Sentry\Severity::debug();
                case \Monolog\Level::Warning:
                    return \Sentry\Severity::warning();
                case \Monolog\Level::Error:
                    return \Sentry\Severity::error();
                case \Monolog\Level::Critical:
                case \Monolog\Level::Alert:
                case \Monolog\Level::Emergency:
                    return \Sentry\Severity::fatal();
                case \Monolog\Level::Info:
                case \Monolog\Level::Notice:
                default:
                    return \Sentry\Severity::info();
            }
        }
    }
} else {
    /**
     * Logic which is used if monolog < 3 is installed.
     *
     * @internal
     */
    trait CompatibilityProcessingHandlerTrait
    {
        /**
         * @param array<string, mixed>|LogRecord $record
         */
        protected abstract function doWrite($record) : void;
        /**
         * {@inheritdoc}
         */
        protected function write(array $record) : void
        {
            $this->doWrite($record);
        }
        /**
         * Translates the Monolog level into the Sentry severity.
         *
         * @param Logger::DEBUG|Logger::INFO|Logger::NOTICE|Logger::WARNING|Logger::ERROR|Logger::CRITICAL|Logger::ALERT|Logger::EMERGENCY $level The Monolog log level
         */
        private static function getSeverityFromLevel(int $level) : \Sentry\Severity
        {
            switch ($level) {
                case \Monolog\Logger::DEBUG:
                    return \Sentry\Severity::debug();
                case \Monolog\Logger::WARNING:
                    return \Sentry\Severity::warning();
                case \Monolog\Logger::ERROR:
                    return \Sentry\Severity::error();
                case \Monolog\Logger::CRITICAL:
                case \Monolog\Logger::ALERT:
                case \Monolog\Logger::EMERGENCY:
                    return \Sentry\Severity::fatal();
                case \Monolog\Logger::INFO:
                case \Monolog\Logger::NOTICE:
                default:
                    return \Sentry\Severity::info();
            }
        }
    }
}
