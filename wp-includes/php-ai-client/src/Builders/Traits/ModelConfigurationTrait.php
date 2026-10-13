<?php

declare (strict_types=1);
namespace WordPress\AiClient\Builders\Traits;

use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
/**
 * Provides shared model configuration methods for builders.
 *
 * Builders that generate results from a model (e.g. {@see \WordPress\AiClient\Builders\PromptBuilder}
 * and {@see \WordPress\AiClient\Builders\EmbeddingBuilder}) use this trait to accumulate model
 * configuration, regardless of how they determine which model to use.
 *
 * @since 1.5.0
 */
trait ModelConfigurationTrait
{
    /**
     * @var ModelConfig The model configuration.
     */
    protected ModelConfig $modelConfig;
    /**
     * Sets the model configuration.
     *
     * Merges the provided configuration with the builder's configuration,
     * with builder configuration taking precedence.
     *
     * @since 0.1.0
     *
     * @param ModelConfig $config The model configuration to merge.
     * @return self
     */
    public function usingModelConfig(ModelConfig $config): self
    {
        $this->mergeModelConfig($config);
        return $this;
    }
    /**
     * Merges the given configuration into the builder's configuration.
     *
     * The builder's own configuration takes precedence for any overlapping settings, so that
     * explicitly configured values are never overwritten by defaults from another source.
     *
     * @since 1.5.0
     *
     * @param ModelConfig $config The model configuration to merge.
     * @return void
     */
    protected function mergeModelConfig(ModelConfig $config): void
    {
        // Merge arrays with builder config taking precedence
        $mergedArray = array_merge($config->toArray(), $this->modelConfig->toArray());
        // Create new config from merged array
        $this->modelConfig = ModelConfig::fromArray($mergedArray);
    }
}
