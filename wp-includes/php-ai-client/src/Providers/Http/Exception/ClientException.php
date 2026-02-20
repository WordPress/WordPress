<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Exception;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Util\ErrorMessageExtractor;
/**
 * Exception thrown for 4xx HTTP client errors.
 *
 * This represents errors where the client request was malformed,
 * unauthorized, forbidden, or otherwise invalid.
 *
 * @since 0.2.0
 */
class ClientException extends InvalidArgumentException
{
    /**
     * The request that failed.
     *
     * @var Request|null
     */
    protected ?Request $request = null;
    /**
     * Returns the request that failed as our Request DTO.
     *
     * @since 0.2.0
     *
     * @return Request
     * @throws \RuntimeException If no request is available
     */
    public function getRequest(): Request
    {
        if ($this->request === null) {
            throw new \RuntimeException('Request object not available. This exception was directly instantiated. ' . 'Use a factory method that provides request context.');
        }
        return $this->request;
    }
    /**
     * Creates a ClientException from a client error response (4xx).
     *
     * This method extracts error details from common API response formats
     * and creates an exception with a descriptive message and status code.
     *
     * @since 0.2.0
     *
     * @param Response $response The HTTP response that failed.
     * @return self
     */
    public static function fromClientErrorResponse(Response $response): self
    {
        $statusCode = $response->getStatusCode();
        $statusTexts = [400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not Found', 422 => 'Unprocessable Entity', 429 => 'Too Many Requests'];
        if (isset($statusTexts[$statusCode])) {
            $errorMessage = sprintf('%s (%d)', $statusTexts[$statusCode], $statusCode);
        } else {
            $errorMessage = sprintf('Client error (%d): Request was rejected due to client-side issue', $statusCode);
        }
        // Extract error message from response data using centralized utility
        $extractedError = ErrorMessageExtractor::extractFromResponseData($response->getData());
        if ($extractedError !== null) {
            $errorMessage .= ' - ' . $extractedError;
        }
        return new self($errorMessage, $statusCode);
    }
}
