<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\ApiBasedImplementation;

use WordPress\AiClient\AiClient;
use WordPress\AiClient\Common\Contracts\CachesDataInterface;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Traits\WithDataCachingTrait;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Http\Contracts\WithHttpTransporterInterface;
use WordPress\AiClient\Providers\Http\Contracts\WithRequestAuthenticationInterface;
use WordPress\AiClient\Providers\Http\Traits\WithHttpTransporterTrait;
use WordPress\AiClient\Providers\Http\Traits\WithRequestAuthenticationTrait;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
/**
 * Base class for an API-based model metadata directory for a provider.
 *
 * @since 0.1.0
 */
abstract class AbstractApiBasedModelMetadataDirectory implements ModelMetadataDirectoryInterface, WithHttpTransporterInterface, WithRequestAuthenticationInterface, CachesDataInterface
{
    use WithHttpTransporterTrait;
    use WithRequestAuthenticationTrait;
    use WithDataCachingTrait;
    /**
     * The cache key suffix for the models list.
     *
     * @since 0.4.0
     *
     * @var string
     */
    private const MODELS_CACHE_KEY = 'models';
    /**
     * Request-local cache for explicit model metadata lookups.
     *
     * @since 1.5.0
     *
     * @var array<string, ModelMetadata|null>
     */
    private array $explicitModelMetadataCache = [];
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public function listModelMetadata(): array
    {
        $modelsMetadata = $this->getModelMetadataMap();
        $explicitModelsMetadata = $this->getExplicitModelMetadataMap(array_keys($modelsMetadata));
        return array_values(array_replace($modelsMetadata, $explicitModelsMetadata));
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public function hasModelMetadata(string $modelId): bool
    {
        if (isset($this->getExplicitModelMetadataMap([$modelId])[$modelId])) {
            return \true;
        }
        $modelsMetadata = $this->getModelMetadataMap();
        return isset($modelsMetadata[$modelId]);
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    final public function getModelMetadata(string $modelId): ModelMetadata
    {
        $explicitModelsMetadata = $this->getExplicitModelMetadataMap([$modelId]);
        if (isset($explicitModelsMetadata[$modelId])) {
            return $explicitModelsMetadata[$modelId];
        }
        $modelsMetadata = $this->getModelMetadataMap();
        if (!isset($modelsMetadata[$modelId])) {
            throw new InvalidArgumentException(sprintf('No model with ID %s was found in the provider', $modelId));
        }
        return $modelsMetadata[$modelId];
    }
    /**
     * Gets explicit model metadata using request-local memoization.
     *
     * @since 1.5.0
     *
     * @param list<string> $modelIds Model IDs for which to get explicit metadata.
     * @return array<string, ModelMetadata> Map of model ID to explicit model metadata.
     */
    private function getExplicitModelMetadataMap(array $modelIds): array
    {
        $uncheckedModelIds = [];
        foreach ($modelIds as $modelId) {
            if (!array_key_exists($modelId, $this->explicitModelMetadataCache)) {
                $uncheckedModelIds[] = $modelId;
            }
        }
        if ($uncheckedModelIds) {
            $explicitModelsMetadata = $this->createModelMetadataForExplicitModelIds($uncheckedModelIds);
            foreach ($uncheckedModelIds as $modelId) {
                $this->explicitModelMetadataCache[$modelId] = $explicitModelsMetadata[$modelId] ?? null;
            }
        }
        return array_filter(array_intersect_key($this->explicitModelMetadataCache, array_flip($modelIds)));
    }
    /**
     * Returns the map of model ID to model metadata for all models from the provider.
     *
     * @since 0.1.0
     *
     * @return array<string, ModelMetadata> Map of model ID to model metadata.
     */
    private function getModelMetadataMap(): array
    {
        /** @var array<string, ModelMetadata> */
        return $this->cached(self::MODELS_CACHE_KEY, fn() => $this->sendListModelsRequest(), 86400);
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.4.0
     */
    protected function getCachedKeys(): array
    {
        return [self::MODELS_CACHE_KEY];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.4.0
     */
    protected function getBaseCacheKey(): string
    {
        return 'ai_client_' . AiClient::VERSION . '_' . md5(static::class);
    }
    /**
     * Creates metadata for explicit model IDs without listing provider models.
     *
     * Providers whose APIs accept arbitrary/current model IDs can override this to avoid a live list-models request
     * when callers already know the model ID they want to instantiate.
     *
     * @since 1.5.0
     *
     * @param list<string> $modelIds The explicit model IDs.
     * @return array<string, ModelMetadata> Map of model ID to model metadata. Omit IDs that should fall back to the
     *                                                provider models list.
     */
    protected function createModelMetadataForExplicitModelIds(array $modelIds): array
    {
        return [];
    }
    /**
     * Sends the API request to list models from the provider and returns the map of model ID to model metadata.
     *
     * @since 0.1.0
     *
     * @return array<string, ModelMetadata> Map of model ID to model metadata.
     */
    abstract protected function sendListModelsRequest(): array;
}
