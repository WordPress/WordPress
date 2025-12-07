<?php

declare(strict_types=1);

namespace Sentry\Util;

use Sentry\Options;

trait PrefixStripper
{
    /**
     * Removes from the given file path the specified prefixes in the SDK options.
     */
    protected function stripPrefixFromFilePath(?Options $options, string $filePath): string
    {
        if ($options === null) {
            return $filePath;
        }

        foreach ($options->getPrefixes() as $prefix) {
            if (mb_substr($filePath, 0, mb_strlen($prefix)) === $prefix) {
                return mb_substr($filePath, mb_strlen($prefix));
            }
        }

        return $filePath;
    }
}
