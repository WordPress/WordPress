<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\Exception;

/**
 *  This class represents an HTTP transport error.
 */
class HttpException extends WebServiceException
{
    /**
     * The URI queried.
     *
     * @var string
     */
    private $uri;
    /**
     * @param string     $message    a message describing the error
     * @param int        $httpStatus the HTTP status code of the response
     * @param string     $uri        the URI used in the request
     * @param \Exception $previous   the previous exception, if any
     */
    public function __construct(string $message, int $httpStatus, string $uri, \Exception $previous = null)
    {
        $this->uri = $uri;
        parent::__construct($message, $httpStatus, $previous);
    }
    public function getUri() : string
    {
        return $this->uri;
    }
    public function getStatusCode() : int
    {
        return $this->getCode();
    }
}
