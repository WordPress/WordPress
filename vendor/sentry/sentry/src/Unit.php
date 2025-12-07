<?php

declare(strict_types=1);

namespace Sentry;

final class Unit implements \Stringable
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

    public static function nanosecond(): self
    {
        return self::getInstance('nanosecond');
    }

    public static function microsecond(): self
    {
        return self::getInstance('microsecond');
    }

    public static function millisecond(): self
    {
        return self::getInstance('millisecond');
    }

    public static function second(): self
    {
        return self::getInstance('second');
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

    public static function bit(): self
    {
        return self::getInstance('bit');
    }

    public static function byte(): self
    {
        return self::getInstance('byte');
    }

    public static function kilobyte(): self
    {
        return self::getInstance('kilobyte');
    }

    public static function kibibyte(): self
    {
        return self::getInstance('kibibyte');
    }

    public static function megabyte(): self
    {
        return self::getInstance('megabyte');
    }

    public static function mebibyte(): self
    {
        return self::getInstance('mebibyte');
    }

    public static function gigabyte(): self
    {
        return self::getInstance('gigabyte');
    }

    public static function gibibyte(): self
    {
        return self::getInstance('gibibyte');
    }

    public static function terabyte(): self
    {
        return self::getInstance('terabyte');
    }

    public static function tebibyte(): self
    {
        return self::getInstance('tebibyte');
    }

    public static function petabyte(): self
    {
        return self::getInstance('petabyte');
    }

    public static function pebibyte(): self
    {
        return self::getInstance('pebibyte');
    }

    public static function exabyte(): self
    {
        return self::getInstance('exabyte');
    }

    public static function exbibyte(): self
    {
        return self::getInstance('exbibyte');
    }

    public static function ratio(): self
    {
        return self::getInstance('ratio');
    }

    public static function percent(): self
    {
        return self::getInstance('percent');
    }

    /**
     * @deprecated `none` is not supported and will be removed in 5.x
     */
    public static function none(): self
    {
        return self::getInstance('none');
    }

    /**
     * @deprecated custom unit types are currently not supported. Will be removed in 5.x
     */
    public static function custom(string $unit): self
    {
        return new self($unit);
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
