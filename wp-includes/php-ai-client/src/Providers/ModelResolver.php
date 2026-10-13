<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\ApiBasedImplementation\Contracts\ApiBasedModelInterface;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelRequirements;
/**
 * Resolves the concrete AI model to use based on selection preferences.
 *
 * This class owns all model-selection state and logic: an explicitly set model,
 * ordered model preferences, a provider constraint, and HTTP request options. It
 * is shared between the builders (via the model resolution trait) so that model
 * selection behaves identically regardless of what is being generated.
 *
 * @since 1.4.0
 *
 * A ProviderModelTuple is a two-element list identifying a model by its provider: index 0 is
 * the provider ID or provider class name, index 1 is the model ID. For example,
 * ['google', 'gemini-embedding-001'].
 *
 * @phpstan-type ProviderModelTuple array{0: string, 1: string}
 */
class ModelResolver
{
    /**
     * @var ProviderRegistry The provider registry for finding suitable models.
     */
    private \WordPress\AiClient\Providers\ProviderRegistry $registry;
    /**
     * @var ModelInterface|null The model to use for generation.
     */
    private ?ModelInterface $model = null;
    /**
     * @var list<string> Ordered list of preference keys to check when selecting a model.
     */
    private array $modelPreferenceKeys = [];
    /**
     * @var string|null The provider ID or class name.
     */
    private ?string $providerIdOrClassName = null;
    /**
     * @var RequestOptions|null The request options for HTTP transport.
     */
    private ?RequestOptions $requestOptions = null;
    /**
     * Constructor.
     *
     * @since 1.4.0
     *
     * @param ProviderRegistry $registry The provider registry for finding suitable models.
     */
    public function __construct(\WordPress\AiClient\Providers\ProviderRegistry $registry)
    {
        $this->registry = $registry;
    }
    /**
     * Creates a deep clone of this resolver.
     *
     * Clones the request options (which contain only primitives). The registry and
     * explicitly set model are service objects and are intentionally NOT cloned, as
     * they should be shared references.
     *
     * @since 1.4.0
     */
    public function __clone()
    {
        if ($this->requestOptions !== null) {
            $this->requestOptions = clone $this->requestOptions;
        }
    }
    /**
     * Sets the model to use for generation.
     *
     * @since 1.4.0
     *
     * @param ModelInterface $model The model to use.
     * @return void
     */
    public function setModel(ModelInterface $model): void
    {
        $this->model = $model;
    }
    /**
     * Gets the explicitly set model, if any.
     *
     * @since 1.4.0
     *
     * @return ModelInterface|null The explicitly set model, or null if none was set.
     */
    public function getModel(): ?ModelInterface
    {
        return $this->model;
    }
    /**
     * Sets preferred models to evaluate in order.
     *
     * @since 1.4.0
     *
     * @param string|ModelInterface|ProviderModelTuple ...$preferredModels The preferred models as model IDs,
     * model instances, or [provider ID, model ID] tuples. For broader compatibility, it is recommended you specify
     * only model IDs or model instances, as that will allow for different providers that expose the same model to be
     * considered.
     * @return void
     *
     * @throws InvalidArgumentException When a preferred model has an invalid type or identifier.
     */
    public function setModelPreferences(...$preferredModels): void
    {
        if ($preferredModels === []) {
            throw new InvalidArgumentException('At least one model preference must be provided.');
        }
        $preferenceKeys = [];
        foreach ($preferredModels as $preferredModel) {
            if (is_array($preferredModel)) {
                // [model identifier, provider ID] tuple
                if (!array_is_list($preferredModel) || count($preferredModel) !== 2) {
                    throw new InvalidArgumentException('Model preference tuple must contain model identifier and provider ID.');
                }
                [$providerId, $modelId] = $preferredModel;
                $modelId = $this->normalizePreferenceIdentifier($modelId);
                $providerId = $this->normalizePreferenceIdentifier($providerId, 'Model preference provider identifiers cannot be empty.');
                $preferenceKey = $this->createProviderModelPreferenceKey($providerId, $modelId);
            } elseif ($preferredModel instanceof ModelInterface) {
                // Model instance
                $modelId = $preferredModel->metadata()->getId();
                $providerId = $preferredModel->providerMetadata()->getId();
                $preferenceKey = $this->createProviderModelPreferenceKey($providerId, $modelId);
            } elseif (is_string($preferredModel)) {
                // Model ID
                $modelId = $this->normalizePreferenceIdentifier($preferredModel);
                $preferenceKey = $this->createModelPreferenceKey($modelId);
            } else {
                // Invalid type
                throw new InvalidArgumentException('Model preferences must be model identifiers, instances of ModelInterface, ' . 'or provider/model tuples.');
            }
            $preferenceKeys[] = $preferenceKey;
        }
        $this->modelPreferenceKeys = $preferenceKeys;
    }
    /**
     * Sets the provider to use for generation.
     *
     * @since 1.4.0
     *
     * @param string $providerIdOrClassName The provider ID or class name.
     * @return void
     */
    public function setProvider(string $providerIdOrClassName): void
    {
        $this->providerIdOrClassName = $providerIdOrClassName;
    }
    /**
     * Sets the request options for HTTP transport.
     *
     * @since 1.4.0
     *
     * @param RequestOptions $requestOptions The request options.
     * @return void
     */
    public function setRequestOptions(RequestOptions $requestOptions): void
    {
        $this->requestOptions = $requestOptions;
    }
    /**
     * Resolves the model to use for the given requirements.
     *
     * If a model has been explicitly set, updates its config, binds dependencies,
     * and returns it. Otherwise, finds a suitable model based on the requirements,
     * honoring any configured model preferences and provider constraint.
     *
     * @since 1.4.0
     *
     * @param ModelRequirements $requirements The requirements the model must satisfy.
     * @param ModelConfig $modelConfig The model configuration to apply.
     * @param string|null $subject Optional caller-specific subject for failure messages.
     * @return ModelInterface The model to use.
     * @throws InvalidArgumentException If no suitable model is found or set model doesn't meet requirements.
     */
    public function resolve(ModelRequirements $requirements, ModelConfig $modelConfig, ?string $subject = null): ModelInterface
    {
        if ($this->model !== null) {
            // Explicit model was provided via setModel(); just update config and bind dependencies.
            $model = $this->model;
            $model->setConfig($modelConfig);
            $this->registry->bindModelDependencies($model);
            $this->bindModelRequestOptions($model);
            return $model;
        }
        // Retrieve the candidate models map which satisfies the requirements.
        $candidateMap = $this->getCandidateModelsMap($requirements);
        if (empty($candidateMap)) {
            // The primary capability is always the first required capability (see
            // ModelRequirements::fromPromptData()/fromEmbeddingData()).
            $requiredCapabilities = $requirements->getRequiredCapabilities();
            $primaryCapability = reset($requiredCapabilities);
            if ($primaryCapability === \false) {
                $message = 'No models found that meet the requested requirements.';
                if ($this->providerIdOrClassName !== null) {
                    $message = sprintf('No models found for provider "%s" that meet the requested requirements.', $this->providerIdOrClassName);
                }
                throw new InvalidArgumentException($message);
            }
            $capabilityValue = $primaryCapability->value;
            $message = sprintf('No models found that support %s.', $capabilityValue);
            if ($subject !== null) {
                $message = sprintf('No models found that support %s for this %s.', $capabilityValue, $subject);
            }
            if ($this->providerIdOrClassName !== null) {
                $message = sprintf('No models found for provider "%s" that support %s.', $this->providerIdOrClassName, $capabilityValue);
                if ($subject !== null) {
                    $message = sprintf('No models found for provider "%s" that support %s for this %s.', $this->providerIdOrClassName, $capabilityValue, $subject);
                }
            }
            throw new InvalidArgumentException($message);
        }
        // Check if any preferred models match the candidates, in priority order.
        if (!empty($this->modelPreferenceKeys)) {
            // Find preferences that match available candidates, preserving preference order.
            $matchingPreferences = array_intersect_key(array_flip($this->modelPreferenceKeys), $candidateMap);
            if (!empty($matchingPreferences)) {
                // Get the first matching preference key
                $firstMatchKey = key($matchingPreferences);
                [$providerId, $modelId] = $candidateMap[$firstMatchKey];
                $model = $this->registry->getProviderModel($providerId, $modelId, $modelConfig);
                $this->bindModelRequestOptions($model);
                return $model;
            }
        }
        // No preference matched; fall back to the first candidate discovered.
        [$providerId, $modelId] = reset($candidateMap);
        $model = $this->registry->getProviderModel($providerId, $modelId, $modelConfig);
        $this->bindModelRequestOptions($model);
        return $model;
    }
    /**
     * Checks whether any model can satisfy the given requirements.
     *
     * @since 1.4.0
     *
     * @param ModelRequirements $requirements The requirements to check support for.
     * @return bool True if the set model or any registered model meets the requirements.
     */
    public function isSupported(ModelRequirements $requirements): bool
    {
        // If the model has been set, check if it meets the requirements
        if ($this->model !== null) {
            return $requirements->areMetBy($this->model->metadata());
        }
        try {
            // Check if any models support these requirements
            $models = $this->registry->findModelsMetadataForSupport($requirements);
            return !empty($models);
        } catch (InvalidArgumentException $e) {
            // No models support the requirements
            return \false;
        }
    }
    /**
     * Binds configured request options to the model if present and supported.
     *
     * Request options are only applicable to API-based models that make HTTP requests.
     *
     * @since 1.4.0
     *
     * @param ModelInterface $model The model to bind request options to.
     * @return void
     */
    private function bindModelRequestOptions(ModelInterface $model): void
    {
        if ($this->requestOptions !== null && $model instanceof ApiBasedModelInterface) {
            $model->setRequestOptions($this->requestOptions);
        }
    }
    /**
     * Builds a map of candidate models that satisfy the requirements for efficient lookup.
     *
     * @since 1.4.0
     *
     * @param ModelRequirements $requirements The requirements derived from the prompt.
     * @return array<string, ProviderModelTuple> Map of preference keys to [providerId, modelId] tuples.
     */
    private function getCandidateModelsMap(ModelRequirements $requirements): array
    {
        if ($this->providerIdOrClassName === null) {
            // No provider locked in, gather all models across providers that meet requirements.
            $providerModelsMetadata = $this->registry->findModelsMetadataForSupport($requirements);
            $candidateMap = [];
            foreach ($providerModelsMetadata as $providerModels) {
                $providerId = $providerModels->getProvider()->getId();
                $providerMap = $this->generateMapFromCandidates($providerId, $providerModels->getModels());
                // Use + operator to merge, preserving keys from $candidateMap (first provider wins for model-only keys)
                $candidateMap = $candidateMap + $providerMap;
            }
            return $candidateMap;
        }
        // Provider set, only consider models from that provider.
        $modelsMetadata = $this->registry->findProviderModelsMetadataForSupport($this->providerIdOrClassName, $requirements);
        // Ensure we pass the provider ID, not the class name
        $providerId = $this->registry->getProviderId($this->providerIdOrClassName);
        return $this->generateMapFromCandidates($providerId, $modelsMetadata);
    }
    /**
     * Generates a candidate map from model metadata with both provider-specific and model-only keys.
     *
     * @since 1.4.0
     *
     * @param string $providerId The provider ID.
     * @param list<ModelMetadata> $modelsMetadata The models metadata to map.
     * @return array<string, ProviderModelTuple> Map of preference keys to [providerId, modelId] tuples.
     */
    private function generateMapFromCandidates(string $providerId, array $modelsMetadata): array
    {
        $map = [];
        foreach ($modelsMetadata as $modelMetadata) {
            $modelId = $modelMetadata->getId();
            // Add provider-specific key
            $providerModelKey = $this->createProviderModelPreferenceKey($providerId, $modelId);
            $map[$providerModelKey] = [$providerId, $modelId];
            // Add model-only key
            $modelKey = $this->createModelPreferenceKey($modelId);
            $map[$modelKey] = [$providerId, $modelId];
        }
        return $map;
    }
    /**
     * Normalizes and validates a preference identifier string.
     *
     * @since 1.4.0
     *
     * @param mixed $value The value to normalize.
     * @param string $emptyMessage The message for empty or invalid values.
     * @return string The normalized identifier.
     *
     * @throws InvalidArgumentException If the value is not a non-empty string.
     */
    private function normalizePreferenceIdentifier($value, string $emptyMessage = 'Model preference identifiers cannot be empty.'): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException($emptyMessage);
        }
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException($emptyMessage);
        }
        return $trimmed;
    }
    /**
     * Creates a preference key for a provider/model combination.
     *
     * @since 1.4.0
     *
     * @param string $providerId The provider identifier.
     * @param string $modelId The model identifier.
     * @return string The generated preference key.
     */
    private function createProviderModelPreferenceKey(string $providerId, string $modelId): string
    {
        return 'providerModel::' . $providerId . '::' . $modelId;
    }
    /**
     * Creates a preference key for a model identifier.
     *
     * @since 1.4.0
     *
     * @param string $modelId The model identifier.
     * @return string The generated preference key.
     */
    private function createModelPreferenceKey(string $modelId): string
    {
        return 'model::' . $modelId;
    }
}
