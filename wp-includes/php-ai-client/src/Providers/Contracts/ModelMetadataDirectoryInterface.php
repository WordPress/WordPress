<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Contracts;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
/**
 * Interface for accessing model metadata within a provider.
 *
 * Provides methods to list, check, and retrieve model metadata
 * for all models supported by a provider.
 *
 * @since 0.1.0
 */
interface ModelMetadataDirectoryInterface
{
    /**
     * Lists all available model metadata.
     *
     * @since 0.1.0
     *
     * @return list<ModelMetadata> Array of model metadata.
     */
    public function listModelMetadata(): array;
    /**
     * Checks if metadata exists for a specific model.
     *
     * @since 0.1.0
     *
     * @param string $modelId Model identifier.
     * @return bool True if metadata exists, false otherwise.
     */
    public function hasModelMetadata(string $modelId): bool;
    /**
     * Gets metadata for a specific model.
     *
     * @since 0.1.0
     *
     * @param string $modelId Model identifier.
     * @return ModelMetadata Model metadata.
     * @throws InvalidArgumentException If model metadata not found.
     */
    public function getModelMetadata(string $modelId): ModelMetadata;
}
