<?php

declare(strict_types=1);

namespace Sentry\Util;

/**
 * This class is an helper utility to parse the version of PHP and convert it
 * to a normalized form.
 *
 * @internal
 */
final class PHPVersion
{
    private const VERSION_PARSING_REGEX = '/^(?<base>\d\.\d\.\d{1,2})(?<extra>-(beta|rc)-?(\d+)?(-dev)?)?/i';

    /**
     * Parses the given string representing a PHP version and returns it in a
     * normalized form.
     *
     * @param string $version The string to parse
     */
    public static function parseVersion(string $version = \PHP_VERSION): string
    {
        if (!preg_match(self::VERSION_PARSING_REGEX, $version, $matches)) {
            return $version;
        }

        $version = $matches['base'];

        if (isset($matches['extra'])) {
            $version .= $matches['extra'];
        }

        return $version;
    }
}
