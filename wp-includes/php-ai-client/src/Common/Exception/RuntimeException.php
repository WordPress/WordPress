<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common\Exception;

use WordPress\AiClient\Common\Contracts\AiClientExceptionInterface;
/**
 * Exception thrown for runtime errors.
 *
 * This extends PHP's built-in RuntimeException while implementing
 * the AI Client exception interface for consistent catch handling.
 *
 * @since 0.2.0
 */
class RuntimeException extends \RuntimeException implements AiClientExceptionInterface
{
}
