<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This enum represents all the possible status of a check in.
 */
final class CheckInStatus implements \Stringable
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

    public static function ok(): self
    {
        return self::getInstance('ok');
    }

    public static function error(): self
    {
        return self::getInstance('error');
    }

    public static function inProgress(): self
    {
        return self::getInstance('in_progress');
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
