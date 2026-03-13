<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This enum represents all the possible types of events that a Sentry server
 * supports.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class EventType implements \Stringable
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

    public static function event(): self
    {
        return self::getInstance('event');
    }

    public static function transaction(): self
    {
        return self::getInstance('transaction');
    }

    public static function checkIn(): self
    {
        return self::getInstance('check_in');
    }

    public static function logs(): self
    {
        return self::getInstance('log');
    }

    public static function metrics(): self
    {
        return self::getInstance('trace_metric');
    }

    /**
     * List of all cases on the enum.
     *
     * @return self[]
     */
    public static function cases(): array
    {
        return [
            self::event(),
            self::transaction(),
            self::checkIn(),
            self::logs(),
            self::metrics(),
        ];
    }

    public function requiresEventId(): bool
    {
        switch ($this) {
            case self::metrics():
            case self::logs():
                return false;
            default:
                return true;
        }
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
