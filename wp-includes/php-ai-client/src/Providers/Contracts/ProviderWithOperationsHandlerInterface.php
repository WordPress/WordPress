<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Contracts;

/**
 * Interface for providers that support operations handlers.
 *
 * Providers implementing this interface can return an operations handler
 * for managing long-running operations across all their models.
 *
 * @since 0.1.0
 */
interface ProviderWithOperationsHandlerInterface
{
    /**
     * Gets the operations handler for this provider.
     *
     * @since 0.1.0
     *
     * @return ProviderOperationsHandlerInterface The operations handler.
     */
    public static function operationsHandler(): \WordPress\AiClient\Providers\Contracts\ProviderOperationsHandlerInterface;
}
