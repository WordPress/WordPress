<?php

declare(strict_types=1);

namespace Sentry;

final class MonitorScheduleUnit implements \Stringable
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

    public static function minute(): self
    {
        return self::getInstance('minute');
    }

    public static function hour(): self
    {
        return self::getInstance('hour');
    }

    public static function day(): self
    {
        return self::getInstance('day');
    }

    public static function week(): self
    {
        return self::getInstance('week');
    }

    public static function month(): self
    {
        return self::getInstance('month');
    }

    public static function year(): self
    {
        return self::getInstance('year');
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
