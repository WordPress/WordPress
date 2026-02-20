<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Contracts;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
/**
 * Interface for AI providers.
 *
 * Providers represent AI services (Google, OpenAI, Anthropic, etc.)
 * and provide access to models, metadata, and availability information.
 *
 * @since 0.1.0
 */
interface ProviderInterface
{
    /**
     * Gets provider metadata.
     *
     * @since 0.1.0
     *
     * @return ProviderMetadata Provider metadata.
     */
    public static function metadata(): ProviderMetadata;
    /**
     * Creates a model instance.
     *
     * @since 0.1.0
     *
     * @param string $modelId Model identifier.
     * @param ?ModelConfig $modelConfig Model configuration.
     * @return ModelInterface Model instance.
     * @throws InvalidArgumentException If model not found or configuration invalid.
     */
    public static function model(string $modelId, ?ModelConfig $modelConfig = null): ModelInterface;
    /**
     * Gets provider availability checker.
     *
     * @since 0.1.0
     *
     * @return ProviderAvailabilityInterface Provider availability checker.
     */
    public static function availability(): \WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
    /**
     * Gets model metadata directory.
     *
     * @since 0.1.0
     *
     * @return ModelMetadataDirectoryInterface Model metadata directory.
     */
    public static function modelMetadataDirectory(): \WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
}
