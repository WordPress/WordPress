<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\Contracts;

use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
/**
 * Interface for AI models.
 *
 * Models represent specific AI models from providers and define
 * their capabilities, configuration, and execution methods.
 *
 * @since 0.1.0
 */
interface ModelInterface
{
    /**
     * Gets model metadata.
     *
     * @since 0.1.0
     *
     * @return ModelMetadata Model metadata.
     */
    public function metadata(): ModelMetadata;
    /**
     * Returns the metadata for the model's provider.
     *
     * @since 0.1.0
     *
     * @return ProviderMetadata The provider metadata.
     */
    public function providerMetadata(): ProviderMetadata;
    /**
     * Sets model configuration.
     *
     * @since 0.1.0
     *
     * @param ModelConfig $config Model configuration.
     * @return void
     */
    public function setConfig(ModelConfig $config): void;
    /**
     * Gets model configuration.
     *
     * @since 0.1.0
     *
     * @return ModelConfig Current model configuration.
     */
    public function getConfig(): ModelConfig;
}
