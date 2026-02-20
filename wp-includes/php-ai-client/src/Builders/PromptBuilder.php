<?php

declare (strict_types=1);
namespace WordPress\AiClient\Builders;

use WordPress\AiClientDependencies\Psr\EventDispatcher\EventDispatcherInterface;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Events\AfterGenerateResultEvent;
use WordPress\AiClient\Events\BeforeGenerateResultEvent;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Files\Enums\FileTypeEnum;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\DTO\UserMessage;
use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\ApiBasedImplementation\Contracts\ApiBasedModelInterface;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelRequirements;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\ImageGeneration\Contracts\ImageGenerationModelInterface;
use WordPress\AiClient\Providers\Models\SpeechGeneration\Contracts\SpeechGenerationModelInterface;
use WordPress\AiClient\Providers\Models\TextGeneration\Contracts\TextGenerationModelInterface;
use WordPress\AiClient\Providers\Models\TextToSpeechConversion\Contracts\TextToSpeechConversionModelInterface;
use WordPress\AiClient\Providers\ProviderRegistry;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
use WordPress\AiClient\Tools\DTO\FunctionDeclaration;
use WordPress\AiClient\Tools\DTO\FunctionResponse;
use WordPress\AiClient\Tools\DTO\WebSearch;
/**
 * Fluent builder for constructing AI prompts.
 *
 * This class provides a fluent interface for building prompts with various
 * content types and model configurations. It automatically infers model
 * requirements based on the features used in the prompt.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type MessageArrayShape from Message
 * @phpstan-import-type MessagePartArrayShape from MessagePart
 *
 * @phpstan-type Prompt string|MessagePart|Message|MessageArrayShape|list<string|MessagePart|MessagePartArrayShape>|list<Message>|null
 */
class PromptBuilder
{
    /**
     * @var ProviderRegistry The provider registry for finding suitable models.
     */
    private ProviderRegistry $registry;
    /**
     * @var list<Message> The messages in the conversation.
     */
    protected array $messages = [];
    /**
     * @var ModelInterface|null The model to use for generation.
     */
    protected ?ModelInterface $model = null;
    /**
     * @var list<string> Ordered list of preference keys to check when selecting a model.
     */
    protected array $modelPreferenceKeys = [];
    /**
     * @var string|null The provider ID or class name.
     */
    protected ?string $providerIdOrClassName = null;
    /**
     * @var ModelConfig The model configuration.
     */
    protected ModelConfig $modelConfig;
    /**
     * @var RequestOptions|null The request options for HTTP transport.
     */
    protected ?RequestOptions $requestOptions = null;
    /**
     * @var EventDispatcherInterface|null The event dispatcher for prompt lifecycle events.
     */
    private ?EventDispatcherInterface $eventDispatcher = null;
    // phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param ProviderRegistry $registry The provider registry for finding suitable models.
     * @param Prompt $prompt Optional initial prompt content.
     * @param EventDispatcherInterface|null $eventDispatcher Optional event dispatcher for lifecycle events.
     */
    // phpcs:enable Generic.Files.LineLength.TooLong
    public function __construct(ProviderRegistry $registry, $prompt = null, ?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->registry = $registry;
        $this->modelConfig = new ModelConfig();
        $this->eventDispatcher = $eventDispatcher;
        if ($prompt === null) {
            return;
        }
        // Check if it's a list of Messages - set as messages
        if ($this->isMessagesList($prompt)) {
            $this->messages = $prompt;
            return;
        }
        // Parse it as a user message
        $userMessage = $this->parseMessage($prompt, MessageRoleEnum::user());
        $this->messages[] = $userMessage;
    }
    /**
     * Creates a deep clone of this builder.
     *
     * Clones all mutable state including messages, model configuration, and request options.
     * Service objects (registry, model, event dispatcher) are intentionally NOT cloned
     * as they are shared dependencies.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        // Deep clone messages array (Message has __clone)
        $clonedMessages = [];
        foreach ($this->messages as $message) {
            $clonedMessages[] = clone $message;
        }
        $this->messages = $clonedMessages;
        // Clone model config (ModelConfig has __clone)
        $this->modelConfig = clone $this->modelConfig;
        // Clone request options if set (contains only primitives)
        if ($this->requestOptions !== null) {
            $this->requestOptions = clone $this->requestOptions;
        }
        // Note: $registry, $model, and $eventDispatcher are service objects
        // and are intentionally NOT cloned - they should be shared references.
    }
    /**
     * Adds text to the current message.
     *
     * @since 0.1.0
     *
     * @param string $text The text to add.
     * @return self
     */
    public function withText(string $text): self
    {
        $part = new MessagePart($text);
        $this->appendPartToMessages($part);
        return $this;
    }
    /**
     * Adds a file to the current message.
     *
     * Accepts:
     * - File object
     * - URL string (remote file)
     * - Base64-encoded data string
     * - Data URI string (data:mime/type;base64,data)
     * - Local file path string
     *
     * @since 0.1.0
     *
     * @param string|File $file The file (File object or string representation).
     * @param string|null $mimeType The MIME type (optional, ignored if File object provided).
     * @return self
     * @throws InvalidArgumentException If the file is invalid or MIME type cannot be determined.
     */
    public function withFile($file, ?string $mimeType = null): self
    {
        $file = $file instanceof File ? $file : new File($file, $mimeType);
        $part = new MessagePart($file);
        $this->appendPartToMessages($part);
        return $this;
    }
    /**
     * Adds a function response to the current message.
     *
     * @since 0.1.0
     *
     * @param FunctionResponse $functionResponse The function response.
     * @return self
     */
    public function withFunctionResponse(FunctionResponse $functionResponse): self
    {
        $part = new MessagePart($functionResponse);
        $this->appendPartToMessages($part);
        return $this;
    }
    /**
     * Adds message parts to the current message.
     *
     * @since 0.1.0
     *
     * @param MessagePart ...$parts The message parts to add.
     * @return self
     */
    public function withMessageParts(MessagePart ...$parts): self
    {
        foreach ($parts as $part) {
            $this->appendPartToMessages($part);
        }
        return $this;
    }
    /**
     * Adds conversation history messages.
     *
     * Historical messages are prepended to the beginning of the message list,
     * before the current message being built.
     *
     * @since 0.1.0
     *
     * @param Message ...$messages The messages to add to history.
     * @return self
     */
    public function withHistory(Message ...$messages): self
    {
        // Prepend the history messages to the beginning of the messages array
        $this->messages = array_merge($messages, $this->messages);
        return $this;
    }
    /**
     * Sets the model to use for generation.
     *
     * The model's configuration will be merged with the builder's configuration,
     * with the builder's configuration taking precedence for any overlapping settings.
     *
     * @since 0.1.0
     *
     * @param ModelInterface $model The model to use.
     * @return self
     */
    public function usingModel(ModelInterface $model): self
    {
        $this->model = $model;
        // Merge model's config with builder's config, with builder's config taking precedence
        $modelConfigArray = $model->getConfig()->toArray();
        $builderConfigArray = $this->modelConfig->toArray();
        $mergedConfigArray = array_merge($modelConfigArray, $builderConfigArray);
        $this->modelConfig = ModelConfig::fromArray($mergedConfigArray);
        return $this;
    }
    /**
     * Sets preferred models to evaluate in order.
     *
     * @since 0.2.0
     *
     * @param string|ModelInterface|array{0:string,1:string} ...$preferredModels The preferred models as model IDs,
     * model instances, or [provider ID, model ID] tuples. For broader compatibility, it is recommended you specify
     * only model IDs or model instances, as that will allow for different providers that expose the same model to be
     * considered.
     * @return self
     *
     * @throws InvalidArgumentException When a preferred model has an invalid type or identifier.
     */
    public function usingModelPreference(...$preferredModels): self
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
        return $this;
    }
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
        // Convert both configs to arrays
        $builderConfigArray = $this->modelConfig->toArray();
        $providedConfigArray = $config->toArray();
        // Merge arrays with builder config taking precedence
        $mergedArray = array_merge($providedConfigArray, $builderConfigArray);
        // Create new config from merged array
        $this->modelConfig = ModelConfig::fromArray($mergedArray);
        return $this;
    }
    /**
     * Sets the provider to use for generation.
     *
     * @since 0.1.0
     *
     * @param string $providerIdOrClassName The provider ID or class name.
     * @return self
     */
    public function usingProvider(string $providerIdOrClassName): self
    {
        $this->providerIdOrClassName = $providerIdOrClassName;
        return $this;
    }
    /**
     * Sets the system instruction.
     *
     * System instructions are stored in the model configuration and guide
     * the AI model's behavior throughout the conversation.
     *
     * @since 0.1.0
     *
     * @param string $systemInstruction The system instruction text.
     * @return self
     */
    public function usingSystemInstruction(string $systemInstruction): self
    {
        $this->modelConfig->setSystemInstruction($systemInstruction);
        return $this;
    }
    /**
     * Sets the maximum number of tokens to generate.
     *
     * @since 0.1.0
     *
     * @param int $maxTokens The maximum number of tokens.
     * @return self
     */
    public function usingMaxTokens(int $maxTokens): self
    {
        $this->modelConfig->setMaxTokens($maxTokens);
        return $this;
    }
    /**
     * Sets the temperature for generation.
     *
     * @since 0.1.0
     *
     * @param float $temperature The temperature value.
     * @return self
     */
    public function usingTemperature(float $temperature): self
    {
        $this->modelConfig->setTemperature($temperature);
        return $this;
    }
    /**
     * Sets the top-p value for generation.
     *
     * @since 0.1.0
     *
     * @param float $topP The top-p value.
     * @return self
     */
    public function usingTopP(float $topP): self
    {
        $this->modelConfig->setTopP($topP);
        return $this;
    }
    /**
     * Sets the top-k value for generation.
     *
     * @since 0.1.0
     *
     * @param int $topK The top-k value.
     * @return self
     */
    public function usingTopK(int $topK): self
    {
        $this->modelConfig->setTopK($topK);
        return $this;
    }
    /**
     * Sets stop sequences for generation.
     *
     * @since 0.1.0
     *
     * @param string ...$stopSequences The stop sequences.
     * @return self
     */
    public function usingStopSequences(string ...$stopSequences): self
    {
        $this->modelConfig->setCustomOption('stopSequences', $stopSequences);
        return $this;
    }
    /**
     * Sets the number of candidates to generate.
     *
     * @since 0.1.0
     *
     * @param int $candidateCount The number of candidates.
     * @return self
     */
    public function usingCandidateCount(int $candidateCount): self
    {
        $this->modelConfig->setCandidateCount($candidateCount);
        return $this;
    }
    /**
     * Sets the function declarations available to the model.
     *
     * @since 0.1.0
     *
     * @param FunctionDeclaration ...$functionDeclarations The function declarations.
     * @return self
     */
    public function usingFunctionDeclarations(FunctionDeclaration ...$functionDeclarations): self
    {
        $this->modelConfig->setFunctionDeclarations($functionDeclarations);
        return $this;
    }
    /**
     * Sets the presence penalty for generation.
     *
     * @since 0.1.0
     *
     * @param float $presencePenalty The presence penalty value.
     * @return self
     */
    public function usingPresencePenalty(float $presencePenalty): self
    {
        $this->modelConfig->setPresencePenalty($presencePenalty);
        return $this;
    }
    /**
     * Sets the frequency penalty for generation.
     *
     * @since 0.1.0
     *
     * @param float $frequencyPenalty The frequency penalty value.
     * @return self
     */
    public function usingFrequencyPenalty(float $frequencyPenalty): self
    {
        $this->modelConfig->setFrequencyPenalty($frequencyPenalty);
        return $this;
    }
    /**
     * Sets the web search configuration.
     *
     * @since 0.1.0
     *
     * @param WebSearch $webSearch The web search configuration.
     * @return self
     */
    public function usingWebSearch(WebSearch $webSearch): self
    {
        $this->modelConfig->setWebSearch($webSearch);
        return $this;
    }
    /**
     * Sets the request options for HTTP transport.
     *
     * @since 0.3.0
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
     * Sets the top log probabilities configuration.
     *
     * If $topLogprobs is null, enables log probabilities.
     * If $topLogprobs has a value, enables log probabilities and sets the number of top log probabilities to return.
     *
     * @since 0.1.0
     *
     * @param int|null $topLogprobs The number of top log probabilities to return, or null to enable log probabilities.
     * @return self
     */
    public function usingTopLogprobs(?int $topLogprobs = null): self
    {
        // Always enable log probabilities
        $this->modelConfig->setLogprobs(\true);
        // If a specific number is provided, set it
        if ($topLogprobs !== null) {
            $this->modelConfig->setTopLogprobs($topLogprobs);
        }
        return $this;
    }
    /**
     * Sets the output MIME type.
     *
     * @since 0.1.0
     *
     * @param string $mimeType The MIME type.
     * @return self
     */
    public function asOutputMimeType(string $mimeType): self
    {
        $this->modelConfig->setOutputMimeType($mimeType);
        return $this;
    }
    /**
     * Sets the output schema.
     *
     * @since 0.1.0
     *
     * @param array<string, mixed> $schema The output schema.
     * @return self
     */
    public function asOutputSchema(array $schema): self
    {
        $this->modelConfig->setOutputSchema($schema);
        return $this;
    }
    /**
     * Sets the output modalities.
     *
     * @since 0.1.0
     *
     * @param ModalityEnum ...$modalities The output modalities.
     * @return self
     */
    public function asOutputModalities(ModalityEnum ...$modalities): self
    {
        $this->modelConfig->setOutputModalities($modalities);
        return $this;
    }
    /**
     * Sets the output file type.
     *
     * @since 0.1.0
     *
     * @param FileTypeEnum $fileType The output file type.
     * @return self
     */
    public function asOutputFileType(FileTypeEnum $fileType): self
    {
        $this->modelConfig->setOutputFileType($fileType);
        return $this;
    }
    /**
     * Configures the prompt for JSON response output.
     *
     * @since 0.1.0
     *
     * @param array<string, mixed>|null $schema Optional JSON schema.
     * @return self
     */
    public function asJsonResponse(?array $schema = null): self
    {
        $this->asOutputMimeType('application/json');
        if ($schema !== null) {
            $this->asOutputSchema($schema);
        }
        return $this;
    }
    /**
     * Infers the capability from configured output modalities.
     *
     * @since 0.1.0
     *
     * @return CapabilityEnum The inferred capability.
     * @throws RuntimeException If the output modality is not supported.
     */
    private function inferCapabilityFromOutputModalities(): CapabilityEnum
    {
        // Get the configured output modalities
        $outputModalities = $this->modelConfig->getOutputModalities();
        // Default to text if no output modality is specified
        if ($outputModalities === null || empty($outputModalities)) {
            return CapabilityEnum::textGeneration();
        }
        // Multi-modal output (multiple modalities) defaults to text generation. This is temporary
        // as a multi-modal interface will be implemented in the future.
        if (count($outputModalities) > 1) {
            return CapabilityEnum::textGeneration();
        }
        // Infer capability from single output modality
        $outputModality = $outputModalities[0];
        if ($outputModality->isText()) {
            return CapabilityEnum::textGeneration();
        } elseif ($outputModality->isImage()) {
            return CapabilityEnum::imageGeneration();
        } elseif ($outputModality->isAudio()) {
            return CapabilityEnum::speechGeneration();
        } elseif ($outputModality->isVideo()) {
            return CapabilityEnum::videoGeneration();
        } else {
            // For unsupported modalities, provide a clear error message
            throw new RuntimeException(sprintf('Output modality "%s" is not yet supported.', $outputModality->value));
        }
    }
    /**
     * Infers the capability from a model's implemented interfaces.
     *
     * @since 0.1.0
     *
     * @param ModelInterface $model The model to infer capability from.
     * @return CapabilityEnum|null The inferred capability, or null if none can be inferred.
     */
    private function inferCapabilityFromModelInterfaces(ModelInterface $model): ?CapabilityEnum
    {
        // Check model interfaces in order of preference
        if ($model instanceof TextGenerationModelInterface) {
            return CapabilityEnum::textGeneration();
        }
        if ($model instanceof ImageGenerationModelInterface) {
            return CapabilityEnum::imageGeneration();
        }
        if ($model instanceof TextToSpeechConversionModelInterface) {
            return CapabilityEnum::textToSpeechConversion();
        }
        if ($model instanceof SpeechGenerationModelInterface) {
            return CapabilityEnum::speechGeneration();
        }
        // No supported interface found
        return null;
    }
    /**
     * Checks if the current prompt is supported by the selected model.
     *
     * @since 0.1.0
     * @since 0.3.0 Method visibility changed to public.
     *
     * @param CapabilityEnum|null $capability Optional capability to check support for.
     * @return bool True if supported, false otherwise.
     */
    public function isSupported(?CapabilityEnum $capability = null): bool
    {
        // If no intended capability provided, infer from output modalities
        if ($capability === null) {
            // First try to infer from a specific model if one is set
            if ($this->model !== null) {
                $inferredCapability = $this->inferCapabilityFromModelInterfaces($this->model);
                if ($inferredCapability !== null) {
                    $capability = $inferredCapability;
                }
            }
            // If still no capability, infer from output modalities
            if ($capability === null) {
                $capability = $this->inferCapabilityFromOutputModalities();
            }
        }
        // Build requirements with the specified capability
        $requirements = ModelRequirements::fromPromptData($capability, $this->messages, $this->modelConfig);
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
     * Checks if the prompt is supported for text generation.
     *
     * @since 0.1.0
     *
     * @return bool True if text generation is supported.
     */
    public function isSupportedForTextGeneration(): bool
    {
        return $this->isSupported(CapabilityEnum::textGeneration());
    }
    /**
     * Checks if the prompt is supported for image generation.
     *
     * @since 0.1.0
     *
     * @return bool True if image generation is supported.
     */
    public function isSupportedForImageGeneration(): bool
    {
        return $this->isSupported(CapabilityEnum::imageGeneration());
    }
    /**
     * Checks if the prompt is supported for text to speech conversion.
     *
     * @since 0.1.0
     *
     * @return bool True if text to speech conversion is supported.
     */
    public function isSupportedForTextToSpeechConversion(): bool
    {
        return $this->isSupported(CapabilityEnum::textToSpeechConversion());
    }
    /**
     * Checks if the prompt is supported for video generation.
     *
     * @since 0.1.0
     *
     * @return bool True if video generation is supported.
     */
    public function isSupportedForVideoGeneration(): bool
    {
        return $this->isSupported(CapabilityEnum::videoGeneration());
    }
    /**
     * Checks if the prompt is supported for speech generation.
     *
     * @since 0.1.0
     *
     * @return bool True if speech generation is supported.
     */
    public function isSupportedForSpeechGeneration(): bool
    {
        return $this->isSupported(CapabilityEnum::speechGeneration());
    }
    /**
     * Checks if the prompt is supported for music generation.
     *
     * @since 0.1.0
     *
     * @return bool True if music generation is supported.
     */
    public function isSupportedForMusicGeneration(): bool
    {
        return $this->isSupported(CapabilityEnum::musicGeneration());
    }
    /**
     * Checks if the prompt is supported for embedding generation.
     *
     * @since 0.1.0
     *
     * @return bool True if embedding generation is supported.
     */
    public function isSupportedForEmbeddingGeneration(): bool
    {
        return $this->isSupported(CapabilityEnum::embeddingGeneration());
    }
    /**
     * Generates a result from the prompt.
     *
     * This is the primary execution method that generates a result (containing
     * potentially multiple candidates) based on the specified capability or
     * the configured output modality.
     *
     * @since 0.1.0
     *
     * @param CapabilityEnum|null $capability Optional capability to use for generation.
     *                                        If null, capability is inferred from output modality.
     * @return GenerativeAiResult The generated result containing candidates.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If the model doesn't support the required capability.
     */
    public function generateResult(?CapabilityEnum $capability = null): GenerativeAiResult
    {
        $this->validateMessages();
        // If capability is not provided, infer it
        if ($capability === null) {
            // First try to infer from a specific model if one is set
            if ($this->model !== null) {
                $inferredCapability = $this->inferCapabilityFromModelInterfaces($this->model);
                if ($inferredCapability !== null) {
                    $capability = $inferredCapability;
                }
            }
            // If still no capability, infer from output modalities
            if ($capability === null) {
                $capability = $this->inferCapabilityFromOutputModalities();
            }
        }
        $model = $this->getConfiguredModel($capability);
        // Dispatch BeforeGenerateResultEvent
        $this->dispatchEvent(new BeforeGenerateResultEvent($this->messages, $model, $capability));
        // Route to the appropriate generation method based on capability
        $result = $this->executeModelGeneration($model, $capability, $this->messages);
        // Dispatch AfterGenerateResultEvent
        $this->dispatchEvent(new AfterGenerateResultEvent($this->messages, $model, $capability, $result));
        return $result;
    }
    /**
     * Executes the model generation based on capability.
     *
     * @since 0.4.0
     *
     * @param ModelInterface $model The model to use for generation.
     * @param CapabilityEnum $capability The capability to use.
     * @param list<Message> $messages The messages to send.
     * @return GenerativeAiResult The generated result.
     * @throws RuntimeException If the model doesn't support the required capability.
     */
    private function executeModelGeneration(ModelInterface $model, CapabilityEnum $capability, array $messages): GenerativeAiResult
    {
        if ($capability->isTextGeneration()) {
            if (!$model instanceof TextGenerationModelInterface) {
                throw new RuntimeException(sprintf('Model "%s" does not support text generation.', $model->metadata()->getId()));
            }
            return $model->generateTextResult($messages);
        }
        if ($capability->isImageGeneration()) {
            if (!$model instanceof ImageGenerationModelInterface) {
                throw new RuntimeException(sprintf('Model "%s" does not support image generation.', $model->metadata()->getId()));
            }
            return $model->generateImageResult($messages);
        }
        if ($capability->isTextToSpeechConversion()) {
            if (!$model instanceof TextToSpeechConversionModelInterface) {
                throw new RuntimeException(sprintf('Model "%s" does not support text-to-speech conversion.', $model->metadata()->getId()));
            }
            return $model->convertTextToSpeechResult($messages);
        }
        if ($capability->isSpeechGeneration()) {
            if (!$model instanceof SpeechGenerationModelInterface) {
                throw new RuntimeException(sprintf('Model "%s" does not support speech generation.', $model->metadata()->getId()));
            }
            return $model->generateSpeechResult($messages);
        }
        // Video generation is not yet implemented
        if ($capability->isVideoGeneration()) {
            throw new RuntimeException('Output modality "video" is not yet supported.');
        }
        // TODO: Add support for other capabilities when interfaces are available
        throw new RuntimeException(sprintf('Capability "%s" is not yet supported for generation.', $capability->value));
    }
    /**
     * Generates a text result from the prompt.
     *
     * @since 0.1.0
     *
     * @return GenerativeAiResult The generated result containing text candidates.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If the model doesn't support text generation.
     */
    public function generateTextResult(): GenerativeAiResult
    {
        // Include text in output modalities
        $this->includeOutputModalities(ModalityEnum::text());
        // Generate and return the result with text generation capability
        return $this->generateResult(CapabilityEnum::textGeneration());
    }
    /**
     * Generates an image result from the prompt.
     *
     * @since 0.1.0
     *
     * @return GenerativeAiResult The generated result containing image candidates.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If the model doesn't support image generation.
     */
    public function generateImageResult(): GenerativeAiResult
    {
        // Include image in output modalities
        $this->includeOutputModalities(ModalityEnum::image());
        // Generate and return the result with image generation capability
        return $this->generateResult(CapabilityEnum::imageGeneration());
    }
    /**
     * Generates a speech result from the prompt.
     *
     * @since 0.1.0
     *
     * @return GenerativeAiResult The generated result containing speech audio candidates.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If the model doesn't support speech generation.
     */
    public function generateSpeechResult(): GenerativeAiResult
    {
        // Include audio in output modalities
        $this->includeOutputModalities(ModalityEnum::audio());
        // Generate and return the result with speech generation capability
        return $this->generateResult(CapabilityEnum::speechGeneration());
    }
    /**
     * Converts text to speech and returns the result.
     *
     * @since 0.1.0
     *
     * @return GenerativeAiResult The generated result containing speech audio candidates.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If the model doesn't support text-to-speech conversion.
     */
    public function convertTextToSpeechResult(): GenerativeAiResult
    {
        // Include audio in output modalities
        $this->includeOutputModalities(ModalityEnum::audio());
        // Generate and return the result with text-to-speech conversion capability
        return $this->generateResult(CapabilityEnum::textToSpeechConversion());
    }
    /**
     * Generates text from the prompt.
     *
     * @since 0.1.0
     *
     * @return string The generated text.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     */
    public function generateText(): string
    {
        return $this->generateTextResult()->toText();
    }
    /**
     * Generates multiple text candidates from the prompt.
     *
     * @since 0.1.0
     *
     * @param int|null $candidateCount The number of candidates to generate.
     * @return list<string> The generated texts.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     */
    public function generateTexts(?int $candidateCount = null): array
    {
        if ($candidateCount !== null) {
            $this->usingCandidateCount($candidateCount);
        }
        // Generate text result
        return $this->generateTextResult()->toTexts();
    }
    /**
     * Generates an image from the prompt.
     *
     * @since 0.1.0
     *
     * @return File The generated image file.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If no image is generated.
     */
    public function generateImage(): File
    {
        return $this->generateImageResult()->toFile();
    }
    /**
     * Generates multiple images from the prompt.
     *
     * @since 0.1.0
     *
     * @param int|null $candidateCount The number of images to generate.
     * @return list<File> The generated image files.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If no images are generated.
     */
    public function generateImages(?int $candidateCount = null): array
    {
        if ($candidateCount !== null) {
            $this->usingCandidateCount($candidateCount);
        }
        return $this->generateImageResult()->toFiles();
    }
    /**
     * Converts text to speech.
     *
     * @since 0.1.0
     *
     * @return File The generated speech audio file.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If no audio is generated.
     */
    public function convertTextToSpeech(): File
    {
        return $this->convertTextToSpeechResult()->toFile();
    }
    /**
     * Converts text to multiple speech outputs.
     *
     * @since 0.1.0
     *
     * @param int|null $candidateCount The number of speech outputs to generate.
     * @return list<File> The generated speech audio files.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If no audio is generated.
     */
    public function convertTextToSpeeches(?int $candidateCount = null): array
    {
        if ($candidateCount !== null) {
            $this->usingCandidateCount($candidateCount);
        }
        return $this->convertTextToSpeechResult()->toFiles();
    }
    /**
     * Generates speech from the prompt.
     *
     * @since 0.1.0
     *
     * @return File The generated speech audio file.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If no audio is generated.
     */
    public function generateSpeech(): File
    {
        return $this->generateSpeechResult()->toFile();
    }
    /**
     * Generates multiple speech outputs from the prompt.
     *
     * @since 0.1.0
     *
     * @param int|null $candidateCount The number of speech outputs to generate.
     * @return list<File> The generated speech audio files.
     * @throws InvalidArgumentException If the prompt or model validation fails.
     * @throws RuntimeException If no audio is generated.
     */
    public function generateSpeeches(?int $candidateCount = null): array
    {
        if ($candidateCount !== null) {
            $this->usingCandidateCount($candidateCount);
        }
        return $this->generateSpeechResult()->toFiles();
    }
    /**
     * Appends a MessagePart to the messages array.
     *
     * If the last message has a user role, the part is added to it.
     * Otherwise, a new UserMessage is created with the part.
     *
     * @since 0.1.0
     *
     * @param MessagePart $part The part to append.
     * @return void
     */
    protected function appendPartToMessages(MessagePart $part): void
    {
        $lastMessage = end($this->messages);
        if ($lastMessage instanceof Message && $lastMessage->getRole()->isUser()) {
            // Replace the last message with a new one containing the appended part
            array_pop($this->messages);
            $this->messages[] = $lastMessage->withPart($part);
            return;
        }
        // Create new UserMessage with the part
        $this->messages[] = new UserMessage([$part]);
    }
    /**
     * Gets the model to use for generation.
     *
     * If a model has been explicitly set, validates it meets requirements and returns it.
     * Otherwise, finds a suitable model based on the prompt requirements.
     *
     * @since 0.1.0
     *
     * @param CapabilityEnum $capability The capability the model will be using.
     * @return ModelInterface The model to use.
     * @throws InvalidArgumentException If no suitable model is found or set model doesn't meet requirements.
     */
    private function getConfiguredModel(CapabilityEnum $capability): ModelInterface
    {
        $requirements = ModelRequirements::fromPromptData($capability, $this->messages, $this->modelConfig);
        if ($this->model !== null) {
            // Explicit model was provided via usingModel(); just update config and bind dependencies.
            $model = $this->model;
            $model->setConfig($this->modelConfig);
            $this->registry->bindModelDependencies($model);
            $this->bindModelRequestOptions($model);
            return $model;
        }
        // Retrieve the candidate models map which satisfies the requirements.
        $candidateMap = $this->getCandidateModelsMap($requirements);
        if (empty($candidateMap)) {
            $message = sprintf('No models found that support %s for this prompt.', $capability->value);
            if ($this->providerIdOrClassName !== null) {
                $message = sprintf('No models found for provider "%s" that support %s for this prompt.', $this->providerIdOrClassName, $capability->value);
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
                $model = $this->registry->getProviderModel($providerId, $modelId, $this->modelConfig);
                $this->bindModelRequestOptions($model);
                return $model;
            }
        }
        // No preference matched; fall back to the first candidate discovered.
        [$providerId, $modelId] = reset($candidateMap);
        $model = $this->registry->getProviderModel($providerId, $modelId, $this->modelConfig);
        $this->bindModelRequestOptions($model);
        return $model;
    }
    /**
     * Binds configured request options to the model if present and supported.
     *
     * Request options are only applicable to API-based models that make HTTP requests.
     *
     * @since 0.3.0
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
     * @since 0.2.0
     *
     * @param ModelRequirements $requirements The requirements derived from the prompt.
     * @return array<string, array{0:string,1:string}> Map of preference keys to [providerId, modelId] tuples.
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
     * @since 0.2.0
     *
     * @param string $providerId The provider ID.
     * @param list<ModelMetadata> $modelsMetadata The models metadata to map.
     * @return array<string, array{0:string,1:string}> Map of preference keys to [providerId, modelId] tuples.
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
     * @since 0.2.0
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
     * @since 0.2.0
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
     * @since 0.2.0
     *
     * @param string $modelId The model identifier.
     * @return string The generated preference key.
     */
    private function createModelPreferenceKey(string $modelId): string
    {
        return 'model::' . $modelId;
    }
    /**
     * Parses various input types into a Message with the given role.
     *
     * @since 0.1.0
     *
     * @param mixed $input The input to parse.
     * @param MessageRoleEnum $defaultRole The role for the message if not specified by input.
     * @return Message The parsed message.
     * @throws InvalidArgumentException If the input type is not supported or results in empty message.
     */
    private function parseMessage($input, MessageRoleEnum $defaultRole): Message
    {
        // Handle Message input directly
        if ($input instanceof Message) {
            return $input;
        }
        // Handle single MessagePart
        if ($input instanceof MessagePart) {
            return new Message($defaultRole, [$input]);
        }
        // Handle string input
        if (is_string($input)) {
            if (trim($input) === '') {
                throw new InvalidArgumentException('Cannot create a message from an empty string.');
            }
            return new Message($defaultRole, [new MessagePart($input)]);
        }
        // Handle array input
        if (!is_array($input)) {
            throw new InvalidArgumentException('Input must be a string, MessagePart, MessagePartArrayShape, ' . 'a list of string|MessagePart|MessagePartArrayShape, or a Message instance.');
        }
        // Handle MessageArrayShape input
        if (Message::isArrayShape($input)) {
            return Message::fromArray($input);
        }
        // Check if it's a MessagePartArrayShape
        if (MessagePart::isArrayShape($input)) {
            return new Message($defaultRole, [MessagePart::fromArray($input)]);
        }
        // It should be a list of string|MessagePart|MessagePartArrayShape
        if (!array_is_list($input)) {
            throw new InvalidArgumentException('Array input must be a list array.');
        }
        // Empty array check
        if (empty($input)) {
            throw new InvalidArgumentException('Cannot create a message from an empty array.');
        }
        $parts = [];
        foreach ($input as $item) {
            if (is_string($item)) {
                $parts[] = new MessagePart($item);
            } elseif ($item instanceof MessagePart) {
                $parts[] = $item;
            } elseif (is_array($item) && MessagePart::isArrayShape($item)) {
                $parts[] = MessagePart::fromArray($item);
            } else {
                throw new InvalidArgumentException('Array items must be strings, MessagePart instances, or MessagePartArrayShape.');
            }
        }
        return new Message($defaultRole, $parts);
    }
    /**
     * Validates the messages array for prompt generation.
     *
     * Ensures that:
     * - The first message is a user message
     * - The last message is a user message
     * - The last message has parts
     *
     * @since 0.1.0
     *
     * @return void
     * @throws InvalidArgumentException If validation fails.
     */
    private function validateMessages(): void
    {
        if (empty($this->messages)) {
            throw new InvalidArgumentException('Cannot generate from an empty prompt. Add content using withText() or similar methods.');
        }
        $firstMessage = reset($this->messages);
        if (!$firstMessage->getRole()->isUser()) {
            throw new InvalidArgumentException('The first message must be from a user role, not from ' . $firstMessage->getRole()->value);
        }
        $lastMessage = end($this->messages);
        if (!$lastMessage->getRole()->isUser()) {
            throw new InvalidArgumentException('The last message must be from a user role, not from ' . $lastMessage->getRole()->value);
        }
        if (empty($lastMessage->getParts())) {
            throw new InvalidArgumentException('The last message must have content parts. Add content using withText() or similar methods.');
        }
    }
    /**
     * Checks if the value is a list of Message objects.
     *
     * @since 0.1.0
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is a list of Message objects.
     *
     * @phpstan-assert-if-true list<Message> $value
     */
    private function isMessagesList($value): bool
    {
        if (!is_array($value) || empty($value) || !array_is_list($value)) {
            return \false;
        }
        // Check if all items are Messages
        foreach ($value as $item) {
            if (!$item instanceof Message) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Includes output modalities if not already present.
     *
     * Adds the given modalities to the output modalities list if they're not
     * already included. If output modalities is null, initializes it with
     * the given modalities.
     *
     * @since 0.1.0
     *
     * @param ModalityEnum ...$modalities The modalities to include.
     * @return void
     */
    private function includeOutputModalities(ModalityEnum ...$modalities): void
    {
        $existing = $this->modelConfig->getOutputModalities();
        // Initialize if null
        if ($existing === null) {
            $this->modelConfig->setOutputModalities($modalities);
            return;
        }
        // Build a set of existing modality values for O(1) lookup
        $existingValues = [];
        foreach ($existing as $existingModality) {
            $existingValues[$existingModality->value] = \true;
        }
        // Add new modalities that don't exist
        $toAdd = [];
        foreach ($modalities as $modality) {
            if (!isset($existingValues[$modality->value])) {
                $toAdd[] = $modality;
            }
        }
        // Update if we have new modalities to add
        if (!empty($toAdd)) {
            $this->modelConfig->setOutputModalities(array_merge($existing, $toAdd));
        }
    }
    /**
     * Dispatches an event if an event dispatcher is registered.
     *
     * @since 0.4.0
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
