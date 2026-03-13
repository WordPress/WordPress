<?php

declare(strict_types=1);

namespace Sentry\Tracing;

/**
 * This enum represents all the possible types of transaction sources.
 *
 * @see https://develop.sentry.dev/sdk/event-payloads/transaction/#transaction-annotations
 */
final class TransactionSource implements \Stringable
{
    /**
     * @var string The value of the enum instance
     */
    private $value;

    /**
     * @var array<string, self> A list of cached enum instances
     */
    private static $instances = [];

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * User-defined name.
     */
    public static function custom(): self
    {
        return self::getInstance('custom');
    }

    /**
     * Raw URL, potentially containing identifiers.
     */
    public static function url(): self
    {
        return self::getInstance('url');
    }

    /**
     * Parametrized URL / route.
     */
    public static function route(): self
    {
        return self::getInstance('route');
    }

    /**
     * Name of the view handling the request.
     */
    public static function view(): self
    {
        return self::getInstance('view');
    }

    /**
     * Named after a software component, such as a function or class name.
     */
    public static function component(): self
    {
        return self::getInstance('component');
    }

    /**
     * Name of a background task (e.g. a Celery task).
     */
    public static function task(): self
    {
        return self::getInstance('task');
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function getInstance(string $value): self
    {
        if (!isset(self::$instances[$value])) {
            self::$instances[$value] = new self($value);
        }

        return self::$instances[$value];
    }
}
