<?php

declare (strict_types=1);
namespace Matomo\Dependencies\MaxMind\Exception;

/**
 * Thrown when the account is out of credits.
 */
class InsufficientFundsException extends InvalidRequestException
{
}
