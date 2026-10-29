<?php

declare (strict_types=1);
namespace WordPress\AiClient\Files\Util;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Files\DTO\File;
/**
 * Resolves developer-supplied file strings into File objects.
 *
 * This utility backs the convenience of passing a plain string to builder methods such as
 * PromptBuilder::withFile(). Because it may read from the local filesystem, it must only be used with
 * strings the developer controls. Never pass untrusted input (user input, API response data); construct
 * a File explicitly via File::fromUrl(), File::fromDataUri() or File::fromBase64() instead.
 *
 * @since 1.5.1
 */
final class FileInputUtil
{
    /**
     * Resolves a file string into a File object.
     *
     * The string is interpreted, in this order, as an http(s) URL, a data URI, a local file path, or plain
     * base64 data (which requires a MIME type). Any other URL scheme is rejected before the filesystem is
     * consulted.
     *
     * @since 1.5.1
     *
     * @param string $input The file string.
     * @param string|null $mimeType The MIME type (optional, required for plain base64 data).
     * @return File The resolved file.
     * @throws InvalidArgumentException If the string is not a supported file representation.
     * @throws RuntimeException If a local file cannot be read.
     */
    public static function fromString(string $input, ?string $mimeType = null): File
    {
        if (preg_match('/^https?:\/\//i', $input) === 1) {
            return File::fromUrl($input, $mimeType);
        }
        if (str_contains($input, '://')) {
            throw new InvalidArgumentException('Unsupported URL scheme. Only http:// and https:// URLs are supported.');
        }
        if (str_starts_with($input, 'data:')) {
            return File::fromDataUri($input, $mimeType);
        }
        // Local paths take precedence over plain base64: an extension-less path can look like base64.
        if (is_file($input)) {
            return File::fromLocalPath($input, $mimeType);
        }
        // Plain base64 (an empty payload is allowed, matching an empty local file).
        if (preg_match('/^[A-Za-z0-9+\/]*={0,2}\z/', $input) === 1) {
            if ($mimeType === null) {
                throw new InvalidArgumentException('MIME type is required when providing plain base64 data without data URI format.');
            }
            return File::fromBase64($input, $mimeType);
        }
        throw new InvalidArgumentException('Invalid file provided. Expected URL, data URI, base64 data, or valid local file path.');
    }
}
