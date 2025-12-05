<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\Exception;

/**
 * This exception is thrown when the service requires permission to access.
 */
class PermissionRequiredException extends InvalidRequestException
{
}
