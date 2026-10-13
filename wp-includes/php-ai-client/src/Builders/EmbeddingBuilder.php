<?php

declare (strict_types=1);
namespace WordPress\AiClient\Builders;

use WordPress\AiClientDependencies\Psr\EventDispatcher\EventDispatcherInterface;
use WordPress\AiClient\Builders\Traits\ModelConfigurationTrait;
use WordPress\AiClient\Common\AbstractEnum;
use WordPress\AiClient\Common\Contracts\AiClientExceptionInterface;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Events\AfterGenerateEmbeddingEvent;
use WordPress\AiClient\Events\BeforeGenerateEmbeddingEvent;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Providers\ApiBasedImplementation\Contracts\ApiBasedModelInterface;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\ModelResolver;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\DTO\ModelRequirements;
use WordPress\AiClient\Providers\Models\DTO\RequiredOption;
use WordPress\AiClient\Providers\Models\EmbeddingGeneration\Contracts\EmbeddingGenerationModelInterface;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\ProviderRegistry;
use WordPress\AiClient\Results\DTO\Embedding;
use WordPress\AiClient\Results\DTO\EmbeddingResult;
/**
 * Fluent builder for generating embeddings.
 *
 * Embeddings transform inputs into vector representations rather than generating a conversational
 * response. Each input is embedded independently, and the builder produces one embedding vector per
 * input.
 *
 * Unlike {@see PromptBuilder}, this builder never selects a model on the caller's behalf. Embedding
 * vectors are only comparable to other vectors produced by the same model, so a stored corpus is
 * permanently bound to the model that created it. Automatically choosing a model could therefore
 * silently invalidate existing vectors, for example when the registered providers change. The model
 * must be specified via {@see self::usingModel()} or {@see self::usingProviderModel()}; the builder
 * verifies that the given model can fulfill the request instead of searching for one that can.
 *
 * @since 1.4.0
 *
 * @phpstan-import-type MessagePartArrayShape from MessagePart
 * @phpstan-import-type UnmetModelRequirementsShape from ModelRequirements
 * @phpstan-import-type ProviderModelTuple from ModelResolver
 *
 * @phpstan-type EmbeddingInput string|MessagePart|File|MessagePartArrayShape
 */
class EmbeddingBuilder
{
    use ModelConfigurationTrait;
    /**
     * @var string Message used when no model was specified.
     */
    private const NO_MODEL_MESSAGE = 'An embedding model must be specified. Embeddings are only comparable to ' . 'other embeddings from the same model, so no model is selected automatically. ' . 'Use usingModel() or usingProviderModel().';
    /**
     * @var ProviderRegistry The provider registry used to prepare the model.
     */
    protected ProviderRegistry $registry;
    /**
     * @var list<MessagePart> The inputs to embed.
     */
    protected array $inputs = [];
    /**
     * @var ModelInterface|null The explicitly provided model, if any.
     */
    protected ?ModelInterface $model = null;
    /**
     * @var ProviderModelTuple|null The provider ID or class name and model ID, if any.
     */
    protected ?array $providerModel = null;
    /**
     * @var RequestOptions|null The request options for HTTP transport.
     */
    protected ?RequestOptions $requestOptions = null;
    /**
     * @var EventDispatcherInterface|null The event dispatcher for embedding lifecycle events.
     */
    private ?EventDispatcherInterface $eventDispatcher;
    /**
     * Constructor.
     *
     * @since 1.4.0
     *
     * @param ProviderRegistry $registry The provider registry used to prepare the model.
     * @param EmbeddingInput|list<EmbeddingInput>|null $input Optional initial input(s) to embed.
     * @param EventDispatcherInterface|null $eventDispatcher Optional event dispatcher for lifecycle events.
     */
    public function __construct(ProviderRegistry $registry, $input = null, ?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->registry = $registry;
        $this->modelConfig = new ModelConfig();
        $this->eventDispatcher = $eventDispatcher;
        if ($input === null) {
            return;
        }
        if (is_array($input) && array_is_list($input)) {
            /** @var list<EmbeddingInput> $input */
            $this->withInput(...$input);
            return;
        }
        /** @var EmbeddingInput $input */
        $this->withInput($input);
    }
    /**
     * Creates a deep clone of this builder.
     *
     * Clones the inputs, model configuration, and request options. Service objects (registry, model,
     * event dispatcher) are intentionally NOT cloned as they are shared dependencies.
     *
     * @since 1.4.0
     */
    public function __clone()
    {
        $clonedInputs = [];
        foreach ($this->inputs as $input) {
            $clonedInputs[] = clone $input;
        }
        $this->inputs = $clonedInputs;
        $this->modelConfig = clone $this->modelConfig;
        if ($this->requestOptions !== null) {
            $this->requestOptions = clone $this->requestOptions;
        }
        // Note: $registry, $model, and $eventDispatcher are service objects and are intentionally
        // NOT cloned - they should be shared references.
    }
    /**
     * Adds one or more inputs to embed.
     *
     * @since 1.4.0
     *
     * @param EmbeddingInput ...$input The inputs to embed, each treated as an independent input.
     * @return self
     * @throws InvalidArgumentException If no inputs are provided or an input is invalid.
     */
    public function withInput(...$input): self
    {
        if ($input === []) {
            throw new InvalidArgumentException('At least one input must be provided.');
        }
        foreach ($input as $singleInput) {
            $this->inputs[] = $this->parseInput($singleInput);
        }
        return $this;
    }
    /**
     * Sets the model to use for embedding generation.
     *
     * The model's configuration will be merged with the builder's configuration,
     * with the builder's configuration taking precedence for any overlapping settings. The merge
     * happens when the model is used rather than here, so replacing the model replaces its
     * configuration along with it.
     *
     * @since 1.4.0
     *
     * @param ModelInterface $model The model to use.
     * @return self
     */
    public function usingModel(ModelInterface $model): self
    {
        $this->model = $model;
        $this->providerModel = null;
        return $this;
    }
    /**
     * Sets the model to use for embedding generation, by provider and model identifier.
     *
     * This is an alternative to {@see self::usingModel()} that does not require the caller to
     * reference a provider class directly. The model is retrieved from the registry when the
     * embeddings are generated, so it receives any configuration set afterwards.
     *
     * @since 1.5.0
     *
     * @param string $providerIdOrClassName The provider ID or class name.
     * @param string $modelId The model identifier.
     * @return self
     * @throws InvalidArgumentException If the provider or model identifier is empty.
     */
    public function usingProviderModel(string $providerIdOrClassName, string $modelId): self
    {
        if (trim($providerIdOrClassName) === '') {
            throw new InvalidArgumentException('Provider identifier cannot be empty.');
        }
        if (trim($modelId) === '') {
            throw new InvalidArgumentException('Model identifier cannot be empty.');
        }
        $this->providerModel = [$providerIdOrClassName, $modelId];
        $this->model = null;
        return $this;
    }
    /**
     * Sets the request options for HTTP transport.
     *
     * @since 1.4.0
     *
     * @param RequestOptions $requestOptions The request options.
     * @return self
     */
    public function usingRequestOptions(RequestOptions $requestOptions): self
    {
        $this->requestOptions = $requestOptions;
        return $this;
    }
    /**
     * Sets the embedding dimensions.
     *
     * @since 1.4.0
     *
     * @param int $dimensions The embedding dimensions.
     * @return self
     */
    public function usingDimensions(int $dimensions): self
    {
        $this->modelConfig->setDimensions($dimensions);
        return $this;
    }
    /**
     * Checks whether the specified model supports the current inputs and configuration.
     *
     * This reports whether the model set via {@see self::usingModel()} or
     * {@see self::usingProviderModel()} can fulfill the request, rather than whether any available
     * model can.
     *
     * Any reason the specified model cannot fulfill the request is reported as `false`, including an
     * unregistered or unconfigured provider, a model ID the provider does not offer, and a provider
     * that could not be reached. Only failing to specify a model at all is treated as a programming
     * error and throws.
     *
     * A model instance provided via {@see self::usingModel()} is left untouched, so that checking
     * for support does not alter it. Determining whether the model's provider is configured may
     * require a request to the provider, which is cached for the remainder of the request.
     *
     * @since 1.4.0
     *
     * @return bool True if the specified model supports embedding generation for the current
     *              inputs and configuration.
     * @throws InvalidArgumentException If no model was specified.
     */
    public function isSupported(): bool
    {
        if ($this->providerModel === null && $this->model === null) {
            throw new InvalidArgumentException(self::NO_MODEL_MESSAGE);
        }
        try {
            $model = $this->locateModel();
        } catch (AiClientExceptionInterface $e) {
            // The model is unusable: its provider is not registered or configured, the provider has
            // no model with the given ID, or the provider could not be reached. Either way it cannot
            // fulfill the request.
            return \false;
        }
        if (!$model instanceof EmbeddingGenerationModelInterface) {
            return \false;
        }
        return $this->describeUnmetRequirements($model) === null;
    }
    /**
     * Generates an embedding result from the configured inputs.
     *
     * @since 1.4.0
     *
     * @return EmbeddingResult The generated embedding result.
     * @throws InvalidArgumentException If no inputs are configured, no model was specified, or the
     *                                  specified model cannot fulfill the request.
     * @throws RuntimeException If the model returns an embedding count that does not match the
     *                          number of inputs.
     */
    public function generateEmbeddingResult(): EmbeddingResult
    {
        if ($this->inputs === []) {
            throw new InvalidArgumentException('Cannot generate embeddings from empty input. Add content using withInput().');
        }
        $capability = CapabilityEnum::embeddingGeneration();
        $model = $this->resolveModel();
        $this->dispatchEvent(new BeforeGenerateEmbeddingEvent($this->inputs, $model, $capability));
        $result = $model->generateEmbeddingResult($this->inputs);
        // Embeddings map positionally to inputs, so the counts must match.
        if (count($result->getEmbeddings()) !== count($this->inputs)) {
            throw new RuntimeException(sprintf('Expected %d embedding(s) from the model, but received %d.', count($this->inputs), count($result->getEmbeddings())));
        }
        $this->dispatchEvent(new AfterGenerateEmbeddingEvent($this->inputs, $model, $capability, $result));
        return $result;
    }
    /**
     * Generates a single embedding from the configured input.
     *
     * @since 1.4.0
     *
     * @return Embedding The generated embedding vector.
     * @throws InvalidArgumentException If no inputs are configured, no model was specified, the
     *                                  specified model cannot fulfill the request, or multiple
     *                                  inputs were provided.
     */
    public function generateEmbedding(): Embedding
    {
        if (count($this->inputs) > 1) {
            throw new InvalidArgumentException('generateEmbedding() returns a single vector; use generateEmbeddings() for multiple inputs.');
        }
        return $this->generateEmbeddingResult()->getEmbedding();
    }
    /**
     * Generates embeddings from the configured inputs.
     *
     * @since 1.4.0
     *
     * @return list<Embedding> The generated embedding vectors.
     * @throws InvalidArgumentException If no inputs are configured, no model was specified, or the
     *                                  specified model cannot fulfill the request.
     * @throws RuntimeException If the model returns an embedding count that does not match the
     *                          number of inputs.
     */
    public function generateEmbeddings(): array
    {
        return $this->generateEmbeddingResult()->getEmbeddings();
    }
    /**
     * Resolves the specified model and verifies it can fulfill the request.
     *
     * @since 1.5.0
     *
     * @return ModelInterface&EmbeddingGenerationModelInterface The verified model.
     * @throws InvalidArgumentException If no model was specified, the model's provider is not
     *                                  configured, or the model cannot fulfill the request.
     */
    private function resolveModel(): ModelInterface
    {
        $model = $this->prepareModel();
        if (!$model instanceof EmbeddingGenerationModelInterface) {
            throw new InvalidArgumentException(sprintf('Model "%s" from provider "%s" does not support embedding generation.', $model->metadata()->getId(), $model->providerMetadata()->getId()));
        }
        $unmetRequirements = $this->describeUnmetRequirements($model);
        if ($unmetRequirements !== null) {
            throw new InvalidArgumentException(sprintf('Model "%s" from provider "%s" cannot fulfill this embedding request. %s', $model->metadata()->getId(), $model->providerMetadata()->getId(), $unmetRequirements));
        }
        return $model;
    }
    /**
     * Locates the specified model, without preparing it for use.
     *
     * Unlike {@see self::prepareModel()}, this leaves a model instance provided by the caller
     * untouched, so that it can be inspected without altering it.
     *
     * @since 1.5.0
     *
     * @return ModelInterface The located model.
     * @throws InvalidArgumentException If no model was specified, the model's provider is not
     *                                  configured, or the model could not be retrieved.
     */
    private function locateModel(): ModelInterface
    {
        if ($this->providerModel !== null) {
            [$providerIdOrClassName, $modelId] = $this->providerModel;
            $this->assertProviderConfigured($providerIdOrClassName);
            // Retrieving the model also binds its provider dependencies.
            return $this->registry->getProviderModel($providerIdOrClassName, $modelId, $this->modelConfig);
        }
        if ($this->model !== null) {
            $model = $this->model;
            $this->assertProviderConfigured($model->providerMetadata()->getId());
            return $model;
        }
        throw new InvalidArgumentException(self::NO_MODEL_MESSAGE);
    }
    /**
     * Prepares the specified model for use, without verifying that it can fulfill the request.
     *
     * @since 1.5.0
     *
     * @return ModelInterface The prepared model, with its dependencies and configuration bound.
     * @throws InvalidArgumentException If no model was specified, the model's provider is not
     *                                  configured, or the model could not be retrieved.
     */
    private function prepareModel(): ModelInterface
    {
        $model = $this->locateModel();
        // A model retrieved from the registry is already bound and configured, so only a model
        // instance provided by the caller needs its dependencies and configuration bound here.
        if ($model === $this->model) {
            $model->setConfig($this->effectiveModelConfig($model));
            $this->registry->bindModelDependencies($model);
        }
        // Request options are only applicable to API-based models that make HTTP requests.
        if ($this->requestOptions !== null && $model instanceof ApiBasedModelInterface) {
            $model->setRequestOptions($this->requestOptions);
        }
        return $model;
    }
    /**
     * Combines a model's own configuration with the configuration set on this builder.
     *
     * The builder's configuration takes precedence for any overlapping settings. Combining happens
     * here, when the model is used, rather than when the model is set, so that the configuration of
     * a model that was subsequently replaced is not applied to its replacement.
     *
     * @since 1.5.0
     *
     * @param ModelInterface $model The model whose configuration to combine with the builder's.
     * @return ModelConfig The effective configuration for the given model.
     */
    private function effectiveModelConfig(ModelInterface $model): ModelConfig
    {
        return ModelConfig::fromArray(array_merge($model->getConfig()->toArray(), $this->modelConfig->toArray()));
    }
    /**
     * Asserts that the given provider is configured and therefore usable.
     *
     * @since 1.5.0
     *
     * @param string $providerIdOrClassName The provider ID or class name.
     * @return void
     * @throws InvalidArgumentException If the provider is not registered or not usable.
     */
    private function assertProviderConfigured(string $providerIdOrClassName): void
    {
        // A provider reports itself unconfigured for any reason it cannot be used, including
        // credentials that are missing, invalid, or rejected, so the message covers all of them.
        if (!$this->registry->isProviderConfigured($providerIdOrClassName)) {
            throw new InvalidArgumentException(sprintf('Provider "%s" is not registered, or is not configured with valid credentials. ' . 'Ensure the provider is registered and its credentials are present and valid ' . 'before generating embeddings.', $providerIdOrClassName));
        }
    }
    /**
     * Describes the requirements of the current request that the given model does not meet.
     *
     * @since 1.5.0
     *
     * @param ModelInterface $model The model to check.
     * @return string|null A description of the unmet requirements, or null if the model meets them all.
     */
    private function describeUnmetRequirements(ModelInterface $model): ?string
    {
        $requirements = ModelRequirements::fromEmbeddingData($this->inputs, $this->effectiveModelConfig($model));
        /** @var UnmetModelRequirementsShape $unmetRequirements */
        $unmetRequirements = $requirements->getUnmetRequirements($model->metadata());
        $descriptions = [];
        if ($unmetRequirements['capabilities'] !== []) {
            $descriptions[] = sprintf('Unsupported capabilities: %s.', implode(', ', array_map(static fn(CapabilityEnum $capability): string => $capability->value, $unmetRequirements['capabilities'])));
        }
        if ($unmetRequirements['options'] !== []) {
            $descriptions[] = sprintf('Unsupported options: %s.', implode(', ', array_map(fn(RequiredOption $option): string => sprintf('%s (%s)', $option->getName()->value, $this->describeOptionValue($option->getValue())), $unmetRequirements['options'])));
        }
        if ($descriptions === []) {
            return null;
        }
        return implode(' ', $descriptions);
    }
    /**
     * Describes an option value for use in an error message.
     *
     * @since 1.5.0
     *
     * @param mixed $value The option value to describe.
     * @return string The human readable description.
     */
    private function describeOptionValue($value): string
    {
        if ($value === null) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value instanceof AbstractEnum) {
            return $value->value;
        }
        if (is_array($value)) {
            return '[' . implode(', ', array_map(fn($item): string => $this->describeOptionValue($item), $value)) . ']';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        return is_object($value) ? get_class($value) : gettype($value);
    }
    /**
     * Parses a single input into a message part.
     *
     * @since 1.4.0
     *
     * @param mixed $input The input to parse. Accepts a string, MessagePart, File, or
     *                     MessagePartArrayShape.
     * @return MessagePart The parsed message part.
     * @throws InvalidArgumentException If the input type is not supported or is an invalid part type.
     */
    private function parseInput($input): MessagePart
    {
        if ($input instanceof MessagePart) {
            return $this->validatePart($input);
        }
        if (is_string($input)) {
            if (trim($input) === '') {
                throw new InvalidArgumentException('Cannot create an embedding input from an empty string.');
            }
            return new MessagePart($input);
        }
        if ($input instanceof File) {
            return new MessagePart($input);
        }
        if (is_array($input) && MessagePart::isArrayShape($input)) {
            return $this->validatePart(MessagePart::fromArray($input));
        }
        throw new InvalidArgumentException('Embedding input must be a string, MessagePart, File, or MessagePartArrayShape.');
    }
    /**
     * Validates that a message part is a valid embedding input.
     *
     * @since 1.4.0
     *
     * @param MessagePart $part The part to validate.
     * @return MessagePart The validated part.
     * @throws InvalidArgumentException If the part is not a text or file part.
     */
    private function validatePart(MessagePart $part): MessagePart
    {
        $type = $part->getType();
        if (!$type->isText() && !$type->isFile()) {
            throw new InvalidArgumentException('Embedding inputs must be text or file parts.');
        }
        return $part;
    }
    /**
     * Dispatches an event if an event dispatcher is registered.
     *
     * @since 1.4.0
     *
     * @param object $event The event to dispatch.
     * @return void
     */
    private function dispatchEvent(object $event): void
    {
        if ($this->eventDispatcher !== null) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
