<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Contracts;

/**
 * Interface for checking provider availability.
 *
 * Determines whether a provider is configured and available
 * for use based on API keys, credentials, or other requirements.
 *
 * @since 0.1.0
 */
interface ProviderAvailabilityInterface
{
    /**
     * Checks if the provider is configured.
     *
     * @since 0.1.0
     *
     * @return bool True if the provider is configured and available, false otherwise.
     */
    public function isConfigured(): bool;
}
