<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Exception;

use WordPress\AiClientDependencies\Psr\Http\Message\RequestInterface;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Http\DTO\Request;
/**
 * Exception thrown for network-related errors.
 *
 * This includes HTTP transport errors, connection failures,
 * timeouts, and other network-related issues.
 *
 * @since 0.2.0
 */
class NetworkException extends RuntimeException
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
     * Creates a NetworkException from a PSR-18 network exception.
     *
     * @since 0.2.0
     *
     * @param RequestInterface $psrRequest The PSR-7 request that failed.
     * @param \Throwable $networkException The PSR-18 network exception.
     * @return self
     */
    public static function fromPsr18NetworkException(RequestInterface $psrRequest, \Throwable $networkException): self
    {
        $request = Request::fromPsrRequest($psrRequest);
        $message = sprintf('Network error occurred while sending request to %s: %s', $request->getUri(), $networkException->getMessage());
        $exception = new self($message, 0, $networkException);
        $exception->request = $request;
        return $exception;
    }
}
