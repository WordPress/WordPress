<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Exception;

use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Http\DTO\Response;
/**
 * Exception thrown for 3xx HTTP redirect responses.
 *
 * This represents cases where the server indicates that the request
 * should be retried at a different location, but automatic redirect
 * handling was not successful or not enabled.
 *
 * @since 0.2.0
 */
class RedirectException extends RuntimeException
{
    /**
     * Creates a RedirectException from a redirect response.
     *
     * This method extracts redirect information from the response headers
     * and creates an exception with a descriptive message and status code.
     *
     * @since 0.2.0
     *
     * @param Response $response The HTTP redirect response.
     * @return self
     */
    public static function fromRedirectResponse(Response $response): self
    {
        $statusCode = $response->getStatusCode();
        $statusTexts = [300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect'];
        if (isset($statusTexts[$statusCode])) {
            $errorMessage = sprintf('%s (%d)', $statusTexts[$statusCode], $statusCode);
        } else {
            $errorMessage = sprintf('Redirect error (%d): Request needs to be retried at a different location', $statusCode);
        }
        // Try to extract the redirect location from headers
        $locationValues = $response->getHeader('Location');
        if ($locationValues !== null && !empty($locationValues)) {
            $location = $locationValues[0];
            $errorMessage .= ' - Location: ' . $location;
        }
        return new self($errorMessage, $statusCode);
    }
}
