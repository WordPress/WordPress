<?php

declare(strict_types=1);

namespace Sentry\Integration;

/**
 * This interface defines a contract that must be implemented by integrations,
 * bindings or hooks that integrate certain frameworks or environments with the SDK.
 */
interface IntegrationInterface
{
    /**
     * Initializes the current integration by registering it once.
     */
    public function setupOnce(): void;
}
