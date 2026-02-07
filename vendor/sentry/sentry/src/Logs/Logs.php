<?php

declare(strict_types=1);

namespace Sentry\Logs;

use Sentry\EventId;

class Logs
{
    /**
     * @var self|null
     */
    private static $instance;

    /**
     * @var LogsAggregator
     */
    private $aggregator;

    private function __construct()
    {
        $this->aggregator = new LogsAggregator();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function trace(string $message, array $values = [], array $attributes = []): void
    {
        $this->aggregator->add(LogLevel::trace(), $message, $values, $attributes);
    }

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function debug(string $message, array $values = [], array $attributes = []): void
    {
        $this->aggregator->add(LogLevel::debug(), $message, $values, $attributes);
    }

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function info(string $message, array $values = [], array $attributes = []): void
    {
        $this->aggregator->add(LogLevel::info(), $message, $values, $attributes);
    }

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function warn(string $message, array $values = [], array $attributes = []): void
    {
        $this->aggregator->add(LogLevel::warn(), $message, $values, $attributes);
    }

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function error(string $message, array $values = [], array $attributes = []): void
    {
        $this->aggregator->add(LogLevel::error(), $message, $values, $attributes);
    }

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function fatal(string $message, array $values = [], array $attributes = []): void
    {
        $this->aggregator->add(LogLevel::fatal(), $message, $values, $attributes);
    }

    /**
     * Flush the captured logs and send them to Sentry.
     */
    public function flush(): ?EventId
    {
        return $this->aggregator->flush();
    }

    /**
     * Get the logs aggregator.
     *
     * @internal
     */
    public function aggregator(): LogsAggregator
    {
        return $this->aggregator;
    }
}
