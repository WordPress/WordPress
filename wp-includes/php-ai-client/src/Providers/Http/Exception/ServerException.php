<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Exception;

use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Util\ErrorMessageExtractor;
/**
 * Exception thrown for 5xx HTTP server errors.
 *
 * This represents errors where the server failed to fulfill
 * a valid request due to internal server errors.
 *
 * @since 0.2.0
 */
class ServerException extends RuntimeException
{
    /**
     * Creates a ServerException from a server error response.
     *
     * This method extracts error details from common API response formats
     * and creates an exception with a descriptive message and status code.
     *
     * @since 0.2.0
     *
     * @param Response $response The HTTP response that failed.
     * @return self
     */
    public static function fromServerErrorResponse(Response $response): self
    {
        $statusCode = $response->getStatusCode();
        $statusTexts = [500 => 'Internal Server Error', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 507 => 'Insufficient Storage'];
        if (isset($statusTexts[$statusCode])) {
            $errorMessage = sprintf('%s (%d)', $statusTexts[$statusCode], $statusCode);
        } else {
            $errorMessage = sprintf('Server error (%d): Request was rejected due to server-side issue', $statusCode);
        }
        // Extract error message from response data using centralized utility
        $extractedError = ErrorMessageExtractor::extractFromResponseData($response->getData());
        if ($extractedError !== null) {
            $errorMessage .= ' - ' . $extractedError;
        }
        return new self($errorMessage, $response->getStatusCode());
    }
}
