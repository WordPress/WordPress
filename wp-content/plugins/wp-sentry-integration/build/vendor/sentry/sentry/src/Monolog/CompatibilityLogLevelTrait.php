<?php

declare (strict_types=1);
namespace Sentry\Monolog;

use Monolog\Level;
use Monolog\Logger;
use Sentry\Logs\LogLevel;
if (\Monolog\Logger::API >= 3) {
    /**
     * Logic which is used if monolog >= 3 is installed.
     *
     * @internal
     */
    trait CompatibilityLogLevelTrait
    {
        /**
         * Translates the Monolog level into the Sentry LogLevel.
         */
        private static function getSentryLogLevelFromMonologLevel(int $level) : \Sentry\Logs\LogLevel
        {
            $level = \Monolog\Level::from($level);
            switch ($level) {
                case \Monolog\Level::Debug:
                    return \Sentry\Logs\LogLevel::debug();
                case \Monolog\Level::Warning:
                    return \Sentry\Logs\LogLevel::warn();
                case \Monolog\Level::Error:
                    return \Sentry\Logs\LogLevel::error();
                case \Monolog\Level::Critical:
                case \Monolog\Level::Alert:
                case \Monolog\Level::Emergency:
                    return \Sentry\Logs\LogLevel::fatal();
                case \Monolog\Level::Info:
                case \Monolog\Level::Notice:
                default:
                    return \Sentry\Logs\LogLevel::info();
            }
        }
    }
} else {
    /**
     * Logic which is used if monolog < 3 is installed.
     *
     * @internal
     */
    trait CompatibilityLogLevelTrait
    {
        /**
         * Translates the Monolog level into the Sentry LogLevel.
         *
         * @param Logger::DEBUG|Logger::INFO|Logger::NOTICE|Logger::WARNING|Logger::ERROR|Logger::CRITICAL|Logger::ALERT|Logger::EMERGENCY $level The Monolog log level
         */
        private static function getSentryLogLevelFromMonologLevel(int $level) : \Sentry\Logs\LogLevel
        {
            switch ($level) {
                case \Monolog\Logger::DEBUG:
                    return \Sentry\Logs\LogLevel::debug();
                case \Monolog\Logger::WARNING:
                    return \Sentry\Logs\LogLevel::warn();
                case \Monolog\Logger::ERROR:
                    return \Sentry\Logs\LogLevel::error();
                case \Monolog\Logger::CRITICAL:
                case \Monolog\Logger::ALERT:
                case \Monolog\Logger::EMERGENCY:
                    return \Sentry\Logs\LogLevel::fatal();
                case \Monolog\Logger::INFO:
                case \Monolog\Logger::NOTICE:
                default:
                    return \Sentry\Logs\LogLevel::info();
            }
        }
    }
}
