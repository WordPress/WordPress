<?php

namespace YoastSEO_Vendor\Psr\Http\Client;

use YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
/**
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * Example: the target host name can not be resolved or the connection failed.
 */
interface NetworkExceptionInterface extends \YoastSEO_Vendor\Psr\Http\Client\ClientExceptionInterface
{
    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     *
     * @return RequestInterface
     */
    public function getRequest() : \YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
}
