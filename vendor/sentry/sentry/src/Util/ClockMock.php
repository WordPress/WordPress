<?php

declare(strict_types=1);

/*
 * Original file: https://github.com/symfony/symfony/blob/9c413a356ccb3c982add8fa2b19813927157aa0e/src/Symfony/Bridge/PhpUnit/ClockMock.php#L18
 *
 * Copyright (c) 2004-present Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Sentry\Util;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Dominic Tubach <dominic.tubach@to.com>
 */
class ClockMock
{
    private static $now;

    public static function withClockMock($enable = null): ?bool
    {
        if ($enable === null) {
            return self::$now !== null;
        }

        self::$now = is_numeric($enable) ? (float) $enable : ($enable ? microtime(true) : null);

        return null;
    }

    public static function time(): int
    {
        if (self::$now === null) {
            return time();
        }

        return (int) self::$now;
    }

    public static function sleep($s): int
    {
        if (self::$now === null) {
            return sleep($s);
        }

        self::$now += (int) $s;

        return 0;
    }

    public static function usleep($us): void
    {
        if (self::$now === null) {
            usleep($us);
        } else {
            self::$now += $us / 1000000;
        }
    }

    /**
     * @return string|float
     */
    public static function microtime($asFloat = false)
    {
        if (self::$now === null) {
            return microtime($asFloat);
        }

        if ($asFloat) {
            return self::$now;
        }

        return \sprintf('%0.6f00 %d', self::$now - (int) self::$now, (int) self::$now);
    }

    public static function date($format, $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }

        return date($format, $timestamp);
    }

    public static function gmdate($format, $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }

        return gmdate($format, $timestamp);
    }

    /**
     * @return array|int|float
     */
    public static function hrtime($asNumber = false)
    {
        $ns = (self::$now - (int) self::$now) * 1000000000;

        if ($asNumber) {
            $number = \sprintf('%d%d', (int) self::$now, $ns);

            return \PHP_INT_SIZE === 8 ? (int) $number : (float) $number;
        }

        return [(int) self::$now, (int) $ns];
    }

    /**
     * @return false|int
     */
    public static function strtotime(string $datetime, ?int $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = self::time();
        }

        return strtotime($datetime, $timestamp);
    }

    public static function register($class): void
    {
        $self = static::class;

        $mockedNs = [substr($class, 0, strrpos($class, '\\'))];
        if (strpos($class, '\\Tests\\') > 0) {
            $ns = str_replace('\\Tests\\', '\\', $class);
            $mockedNs[] = substr($ns, 0, strrpos($ns, '\\'));
        } elseif (strpos($class, 'Tests\\') === 0) {
            $mockedNs[] = substr($class, 6, strrpos($class, '\\') - 6);
        }
        foreach ($mockedNs as $ns) {
            if (\function_exists($ns . '\time')) {
                continue;
            }
            eval(<<<EOPHP
namespace $ns;

function time()
{
    return \\$self::time();
}

function microtime(\$asFloat = false)
{
    return \\$self::microtime(\$asFloat);
}

function sleep(\$s)
{
    return \\$self::sleep(\$s);
}

function usleep(\$us)
{
    \\$self::usleep(\$us);
}

function date(\$format, \$timestamp = null)
{
    return \\$self::date(\$format, \$timestamp);
}

function gmdate(\$format, \$timestamp = null)
{
    return \\$self::gmdate(\$format, \$timestamp);
}

function hrtime(\$asNumber = false)
{
    return \\$self::hrtime(\$asNumber);
}

function strtotime(\$datetime, \$timestamp = null)
{
    return \\$self::strtotime(\$datetime, \$timestamp);
}
EOPHP
            );
        }
    }
}
