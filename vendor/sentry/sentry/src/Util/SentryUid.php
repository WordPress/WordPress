<?php

declare(strict_types=1);

namespace Sentry\Util;

/**
 * @internal
 */
final class SentryUid
{
    /**
     * Generate a random "Sentry UID", a UUID version 4 without dashes.
     *
     * @copyright Fabien Potencier MIT License https://github.com/symfony/polyfill/blob/main/LICENSE
     */
    public static function generate(): string
    {
        if (\function_exists('uuid_create')) {
            return strtolower(str_replace('-', '', uuid_create(\UUID_TYPE_RANDOM)));
        }

        $uuid = bin2hex(random_bytes(16));

        return \sprintf('%08s%04s4%03s%04x%012s',
            // 32 bits for "time_low"
            substr($uuid, 0, 8),
            // 16 bits for "time_mid"
            substr($uuid, 8, 4),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            substr($uuid, 13, 3),
            // 16 bits:
            // * 8 bits for "clk_seq_hi_res",
            // * 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            hexdec(substr($uuid, 16, 4)) & 0x3FFF | 0x8000,
            // 48 bits for "node"
            substr($uuid, 20, 12)
        );
    }
}
