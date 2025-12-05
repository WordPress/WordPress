<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\Exception;

/**
 * This class represents an error in creating the request to be sent to the
 * web service. For example, if the array cannot be encoded as JSON or if there
 * is a missing or invalid field.
 */
class InvalidInputException extends WebServiceException
{
}
