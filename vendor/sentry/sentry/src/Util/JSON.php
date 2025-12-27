<?php

declare(strict_types=1);

namespace Sentry\Util;

use Sentry\Exception\JsonException;

/**
 * This class provides some utility methods to encode/decode JSON data.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 *
 * @internal
 */
final class JSON
{
    /**
     * Encodes the given data into JSON.
     *
     * @param mixed $data     The data to encode
     * @param int   $options  Bitmask consisting of JSON_* constants
     * @param int   $maxDepth The maximum depth allowed for serializing $data
     *
     * @throws JsonException If the encoding failed
     */
    public static function encode($data, int $options = 0, int $maxDepth = 512): string
    {
        if ($maxDepth < 1) {
            throw new \InvalidArgumentException('The $maxDepth argument must be an integer greater than 0.');
        }

        $options |= \JSON_UNESCAPED_UNICODE | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_PARTIAL_OUTPUT_ON_ERROR;

        $encodedData = json_encode($data, $options, $maxDepth);

        $allowedErrors = [\JSON_ERROR_NONE, \JSON_ERROR_RECURSION, \JSON_ERROR_INF_OR_NAN, \JSON_ERROR_UNSUPPORTED_TYPE];
        if (\defined('JSON_ERROR_NON_BACKED_ENUM')) {
            $allowedErrors[] = \JSON_ERROR_NON_BACKED_ENUM;
        }

        $encounteredAnyError = json_last_error() !== \JSON_ERROR_NONE;

        if (($encounteredAnyError && ($encodedData === 'null' || $encodedData === false)) || !\in_array(json_last_error(), $allowedErrors, true)) {
            throw new JsonException(\sprintf('Could not encode value into JSON format. Error was: "%s".', json_last_error_msg()));
        }

        return $encodedData;
    }

    /**
     * Decodes the given data from JSON.
     *
     * @param string $data The data to decode
     *
     * @return mixed
     *
     * @throws JsonException If the decoding failed
     */
    public static function decode(string $data)
    {
        $decodedData = json_decode($data, true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new JsonException(\sprintf('Could not decode value from JSON format. Error was: "%s".', json_last_error_msg()));
        }

        return $decodedData;
    }
}
