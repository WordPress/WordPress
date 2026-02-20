<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common\Contracts;

use Throwable;
/**
 * Base interface for all AI Client exceptions.
 *
 * This interface allows callers to catch all AI Client specific exceptions
 * with a single catch statement.
 *
 * @since 0.2.0
 */
interface AiClientExceptionInterface extends Throwable
{
}
