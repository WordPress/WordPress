<?php

declare(strict_types=1);

namespace Sentry\Util;

class PHPConfiguration
{
    public static function isBooleanIniOptionEnabled(string $option): bool
    {
        $value = \ini_get($option);

        if (empty($value)) {
            return false;
        }

        // https://www.php.net/manual/en/function.ini-get.php#refsect1-function.ini-get-notes
        return \in_array(strtolower($value), ['1', 'on', 'true'], true);
    }
}
