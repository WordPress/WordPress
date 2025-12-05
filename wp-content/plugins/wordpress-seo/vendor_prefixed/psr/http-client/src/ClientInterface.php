<?php

namespace YoastSEO_Vendor\Psr\Http\Client;

use YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
use YoastSEO_Vendor\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(\YoastSEO_Vendor\Psr\Http\Message\RequestInterface $request) : \YoastSEO_Vendor\Psr\Http\Message\ResponseInterface;
}
