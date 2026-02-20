<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Contracts;

use WordPress\AiClientDependencies\Psr\Http\Message\RequestInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\ResponseInterface;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
/**
 * Interface for HTTP clients that support per-request transport options.
 *
 * Extends the capabilities of PSR-18 clients by allowing custom transport
 * configuration such as timeouts and redirect handling on each request.
 *
 * @since 0.2.0
 */
interface ClientWithOptionsInterface
{
    /**
     * Sends an HTTP request with the given transport options.
     *
     * @since 0.2.0
     *
     * @param RequestInterface $request The PSR-7 request to send.
     * @param RequestOptions $options The request transport options. Must not be null.
     * @return ResponseInterface The PSR-7 response received.
     */
    public function sendRequestWithOptions(RequestInterface $request, RequestOptions $options): ResponseInterface;
}
