<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\ApiBasedImplementation;

use WordPress\AiClient\Providers\AbstractProvider;
/**
 * Base class for API-based providers.
 *
 * This abstract class provides URL construction utilities for providers that
 * communicate with REST APIs. It standardizes the pattern of combining a base
 * URL with endpoint paths.
 *
 * @since 0.2.0
 */
abstract class AbstractApiProvider extends AbstractProvider
{
    /**
     * Gets the base URL for the provider's API.
     *
     * The base URL should include the protocol and domain, and may include
     * the API version path (e.g., "https://api.example.com/v1").
     *
     * @since 0.2.0
     *
     * @return string The base URL for the provider's API.
     */
    abstract protected static function baseUrl(): string;
    /**
     * Constructs a full URL by combining the base URL with an optional path.
     *
     * This method ensures proper URL construction by:
     * - Using the provider's base URL
     * - Trimming leading slashes from the path to prevent double-slashes
     * - Joining the base URL and path with a single forward slash
     *
     * @since 0.2.0
     *
     * @param string $path Optional path to append to the base URL. Default empty string.
     * @return string The complete URL.
     */
    public static function url(string $path = ''): string
    {
        if ($path === '') {
            return static::baseUrl();
        }
        return static::baseUrl() . '/' . ltrim($path, '/');
    }
}
