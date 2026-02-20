<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Util;

use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Exception\ClientException;
use WordPress\AiClient\Providers\Http\Exception\RedirectException;
use WordPress\AiClient\Providers\Http\Exception\ServerException;
/**
 * Class with static utility methods to process HTTP responses.
 *
 * @since 0.1.0
 */
class ResponseUtil
{
    /**
     * Throws an appropriate exception if the given response is not successful.
     *
     * This method checks the HTTP status code of the response and throws
     * the appropriate exception type based on the status code range:
     * - 3xx: RedirectException (redirect responses)
     * - 4xx: ClientException (client errors)
     * - 5xx: ServerException (server errors)
     * - Other unsuccessful responses: RuntimeException (invalid status codes)
     *
     * @since 0.1.0
     *
     * @param Response $response The HTTP response to check.
     * @throws RedirectException If the response indicates a redirect (3xx).
     * @throws ClientException If the response indicates a client error (4xx).
     * @throws ServerException If the response indicates a server error (5xx).
     * @throws \RuntimeException If the response has an invalid status code.
     */
    public static function throwIfNotSuccessful(Response $response): void
    {
        if ($response->isSuccessful()) {
            return;
        }
        $statusCode = $response->getStatusCode();
        // 3xx Redirect Responses
        if ($statusCode >= 300 && $statusCode < 400) {
            throw RedirectException::fromRedirectResponse($response);
        }
        // 4xx Client Errors
        if ($statusCode >= 400 && $statusCode < 500) {
            throw ClientException::fromClientErrorResponse($response);
        }
        // 5xx Server Errors
        if ($statusCode >= 500 && $statusCode < 600) {
            throw ServerException::fromServerErrorResponse($response);
        }
        throw new \RuntimeException(sprintf('Response returned invalid status code: %s', $response->getStatusCode()));
    }
}
