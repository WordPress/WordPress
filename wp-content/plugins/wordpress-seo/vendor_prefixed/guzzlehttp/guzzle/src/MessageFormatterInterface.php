<?php

namespace YoastSEO_Vendor\GuzzleHttp;

use YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
use YoastSEO_Vendor\Psr\Http\Message\ResponseInterface;
interface MessageFormatterInterface
{
    /**
     * Returns a formatted message string.
     *
     * @param RequestInterface       $request  Request that was sent
     * @param ResponseInterface|null $response Response that was received
     * @param \Throwable|null        $error    Exception that was received
     */
    public function format(\YoastSEO_Vendor\Psr\Http\Message\RequestInterface $request, \YoastSEO_Vendor\Psr\Http\Message\ResponseInterface $response = null, \Throwable $error = null) : string;
}
