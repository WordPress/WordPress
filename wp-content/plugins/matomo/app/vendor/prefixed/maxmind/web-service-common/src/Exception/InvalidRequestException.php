<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\Exception;

/**
 * Thrown when a MaxMind web service returns an error relating to the request.
 */
class InvalidRequestException extends HttpException
{
    /**
     * The code returned by the MaxMind web service.
     *
     * @var string
     */
    private $error;
    /**
     * @param string     $message    the exception message
     * @param string     $error      the error code returned by the MaxMind web service
     * @param int        $httpStatus the HTTP status code of the response
     * @param string     $uri        the URI queries
     * @param \Exception $previous   the previous exception, if any
     */
    public function __construct(string $message, string $error, int $httpStatus, string $uri, \Exception $previous = null)
    {
        $this->error = $error;
        parent::__construct($message, $httpStatus, $uri, $previous);
    }
    public function getErrorCode() : string
    {
        return $this->error;
    }
}
