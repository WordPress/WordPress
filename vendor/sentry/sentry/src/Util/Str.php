<?php

declare(strict_types=1);

namespace Sentry\Util;

/**
 * This class provides some utility methods to work with strings.
 *
 * @internal
 */
class Str
{
    /**
     * Safe way of running `vsprintf` without throwing exceptions or errors.
     *
     * If the string could not be formatted, it returns `null`.
     *
     * @see https://www.php.net/manual/en/function.vsprintf.php
     *
     * @param array<bool|float|int|string|null> $values
     */
    public static function vsprintfOrNull(string $message, array $values): ?string
    {
        if (empty($values)) {
            return $message;
        }

        foreach ($values as $value) {
            // If the value is not a scalar or null, we cannot safely format it
            if (!\is_scalar($value) && $value !== null) {
                return null;
            }
        }

        try {
            $result = @vsprintf($message, $values);

            // @phpstan-ignore-next-line on PHP 7 `vsprintf` does not throw an exception but can return `false`
            return $result === false ? null : $result;
        } catch (\Error $e) { // This is technically a `ValueError` in PHP 8.0+ but this works in PHP 7 as well
            return null;
        }
    }
}
