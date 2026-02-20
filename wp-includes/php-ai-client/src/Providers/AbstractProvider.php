<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers;

use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\Contracts\ProviderInterface;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
/**
 * Base class for a provider.
 *
 * @since 0.1.0
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var array<string, ProviderMetadata> Cache for provider metadata per class.
     */
    private static array $metadataCache = [];
    /**
     * @var array<string, ProviderAvailabilityInterface> Cache for provider availability per class.
     */
    private static array $availabilityCache = [];
    /**
     * @var array<string, ModelMetadataDirectoryInterface> Cache for model metadata directory per class.
     */
    private static array $modelMetadataDirectoryCache = [];
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public static function metadata(): ProviderMetadata
    {
        $className = static::class;
        if (!isset(self::$metadataCache[$className])) {
            self::$metadataCache[$className] = static::createProviderMetadata();
        }
        return self::$metadataCache[$className];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public static function model(string $modelId, ?ModelConfig $modelConfig = null): ModelInterface
    {
        $providerMetadata = static::metadata();
        $modelMetadata = static::modelMetadataDirectory()->getModelMetadata($modelId);
        $model = static::createModel($modelMetadata, $providerMetadata);
        if ($modelConfig) {
            $model->setConfig($modelConfig);
        }
        return $model;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public static function availability(): ProviderAvailabilityInterface
    {
        $className = static::class;
        if (!isset(self::$availabilityCache[$className])) {
            self::$availabilityCache[$className] = static::createProviderAvailability();
        }
        return self::$availabilityCache[$className];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public static function modelMetadataDirectory(): ModelMetadataDirectoryInterface
    {
        $className = static::class;
        if (!isset(self::$modelMetadataDirectoryCache[$className])) {
            self::$modelMetadataDirectoryCache[$className] = static::createModelMetadataDirectory();
        }
        return self::$modelMetadataDirectoryCache[$className];
    }
    /**
     * Creates a model instance based on the given model metadata and provider metadata.
     *
     * @since 0.1.0
     *
     * @param ModelMetadata $modelMetadata The model metadata.
     * @param ProviderMetadata $providerMetadata The provider metadata.
     * @return ModelInterface The new model instance.
     */
    abstract protected static function createModel(ModelMetadata $modelMetadata, ProviderMetadata $providerMetadata): ModelInterface;
    /**
     * Creates the provider metadata instance.
     *
     * @since 0.1.0
     *
     * @return ProviderMetadata The provider metadata.
     */
    abstract protected static function createProviderMetadata(): ProviderMetadata;
    /**
     * Creates the provider availability instance.
     *
     * @since 0.1.0
     *
     * @return ProviderAvailabilityInterface The provider availability.
     */
    abstract protected static function createProviderAvailability(): ProviderAvailabilityInterface;
    /**
     * Creates the model metadata directory instance.
     *
     * @since 0.1.0
     *
     * @return ModelMetadataDirectoryInterface The model metadata directory.
     */
    abstract protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface;
}
