<?php

declare (strict_types=1);
namespace WordPress\AiClient\Results\Contracts;

use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Results\DTO\TokenUsage;
/**
 * Interface for AI operation results.
 *
 * Results contain the output from AI operations along with metadata
 * such as token usage and provider-specific information.
 *
 * @since 0.1.0
 */
interface ResultInterface
{
    /**
     * Gets the result ID.
     *
     * @since 0.1.0
     *
     * @return string The unique result identifier.
     */
    public function getId(): string;
    /**
     * Gets token usage information.
     *
     * @since 0.1.0
     *
     * @return TokenUsage Token usage statistics.
     */
    public function getTokenUsage(): TokenUsage;
    /**
     * Gets the provider metadata.
     *
     * @since 0.1.0
     *
     * @return ProviderMetadata The provider metadata.
     */
    public function getProviderMetadata(): ProviderMetadata;
    /**
     * Gets the model metadata.
     *
     * @since 0.1.0
     *
     * @return ModelMetadata The model metadata.
     */
    public function getModelMetadata(): ModelMetadata;
    /**
     * Gets provider-specific metadata.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed> Provider metadata.
     */
    public function getAdditionalData(): array;
}
