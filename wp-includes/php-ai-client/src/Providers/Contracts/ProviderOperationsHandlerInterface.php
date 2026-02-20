<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Contracts;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Operations\Contracts\OperationInterface;
/**
 * Interface for handling provider-level operations.
 *
 * Provides methods to retrieve and manage long-running operations
 * across all models within a provider. Operations are tracked at the
 * provider level rather than per-model.
 *
 * @since 0.1.0
 */
interface ProviderOperationsHandlerInterface
{
    /**
     * Gets an operation by ID.
     *
     * @since 0.1.0
     *
     * @param string $operationId Operation identifier.
     * @return OperationInterface The operation.
     * @throws InvalidArgumentException If operation not found.
     */
    public function getOperation(string $operationId): OperationInterface;
}
