<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Contracts;

use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Http\DTO\Response;
/**
 * Interface for HTTP transport implementations.
 *
 * Handles sending HTTP requests and receiving responses using
 * PSR-7, PSR-17, and PSR-18 standards internally.
 *
 * @since 0.1.0
 */
interface HttpTransporterInterface
{
    /**
     * Sends an HTTP request and returns the response.
     *
     * @since 0.1.0
     *
     * @param Request $request The request to send.
     * @param RequestOptions|null $options Optional transport options for the request.
     * @return Response The response received.
     */
    public function send(Request $request, ?RequestOptions $options = null): Response;
}
