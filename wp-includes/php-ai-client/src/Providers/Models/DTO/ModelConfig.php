<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Files\Enums\FileTypeEnum;
use WordPress\AiClient\Files\Enums\MediaOrientationEnum;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Tools\DTO\FunctionDeclaration;
use WordPress\AiClient\Tools\DTO\WebSearch;
/**
 * Represents configuration for an AI model.
 *
 * This class allows configuring various parameters for model behavior,
 * including output modalities, system instructions, generation parameters,
 * and tool integrations.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type FunctionDeclarationArrayShape from FunctionDeclaration
 * @phpstan-import-type WebSearchArrayShape from WebSearch
 *
 * @phpstan-type ModelConfigArrayShape array{
 *     outputModalities?: list<string>,
 *     systemInstruction?: string,
 *     candidateCount?: int,
 *     maxTokens?: int,
 *     temperature?: float,
 *     topP?: float,
 *     topK?: int,
 *     stopSequences?: list<string>,
 *     presencePenalty?: float,
 *     frequencyPenalty?: float,
 *     logprobs?: bool,
 *     topLogprobs?: int,
 *     functionDeclarations?: list<FunctionDeclarationArrayShape>,
 *     webSearch?: WebSearchArrayShape,
 *     outputFileType?: string,
 *     outputMimeType?: string,
 *     outputSchema?: array<string, mixed>,
 *     outputMediaOrientation?: string,
 *     outputMediaAspectRatio?: string,
 *     outputSpeechVoice?: string,
 *     customOptions?: array<string, mixed>
 * }
 *
 * @extends AbstractDataTransferObject<ModelConfigArrayShape>
 */
class ModelConfig extends AbstractDataTransferObject
{
    public const KEY_OUTPUT_MODALITIES = 'outputModalities';
    public const KEY_SYSTEM_INSTRUCTION = 'systemInstruction';
    public const KEY_CANDIDATE_COUNT = 'candidateCount';
    public const KEY_MAX_TOKENS = 'maxTokens';
    public const KEY_TEMPERATURE = 'temperature';
    public const KEY_TOP_P = 'topP';
    public const KEY_TOP_K = 'topK';
    public const KEY_STOP_SEQUENCES = 'stopSequences';
    public const KEY_PRESENCE_PENALTY = 'presencePenalty';
    public const KEY_FREQUENCY_PENALTY = 'frequencyPenalty';
    public const KEY_LOGPROBS = 'logprobs';
    public const KEY_TOP_LOGPROBS = 'topLogprobs';
    public const KEY_FUNCTION_DECLARATIONS = 'functionDeclarations';
    public const KEY_WEB_SEARCH = 'webSearch';
    public const KEY_OUTPUT_FILE_TYPE = 'outputFileType';
    public const KEY_OUTPUT_MIME_TYPE = 'outputMimeType';
    public const KEY_OUTPUT_SCHEMA = 'outputSchema';
    public const KEY_OUTPUT_MEDIA_ORIENTATION = 'outputMediaOrientation';
    public const KEY_OUTPUT_MEDIA_ASPECT_RATIO = 'outputMediaAspectRatio';
    public const KEY_OUTPUT_SPEECH_VOICE = 'outputSpeechVoice';
    public const KEY_CUSTOM_OPTIONS = 'customOptions';
    /*
     * Note: This key is not an actual model config key, but specified here for convenience.
     * It is relevant for model discovery, to determine which models support which input modalities.
     * The actual input modalities are part of the message sent to the model, not the model config.
     */
    public const KEY_INPUT_MODALITIES = 'inputModalities';
    /**
     * @var list<ModalityEnum>|null Output modalities for the model.
     */
    protected ?array $outputModalities = null;
    /**
     * @var string|null System instruction for the model.
     */
    protected ?string $systemInstruction = null;
    /**
     * @var int|null Number of response candidates to generate.
     */
    protected ?int $candidateCount = null;
    /**
     * @var int|null Maximum number of tokens to generate.
     */
    protected ?int $maxTokens = null;
    /**
     * @var float|null Temperature for randomness (0.0 to 2.0).
     */
    protected ?float $temperature = null;
    /**
     * @var float|null Top-p nucleus sampling parameter.
     */
    protected ?float $topP = null;
    /**
     * @var int|null Top-k sampling parameter.
     */
    protected ?int $topK = null;
    /**
     * @var list<string>|null Stop sequences.
     */
    protected ?array $stopSequences = null;
    /**
     * @var float|null Presence penalty for reducing repetition.
     */
    protected ?float $presencePenalty = null;
    /**
     * @var float|null Frequency penalty for reducing repetition.
     */
    protected ?float $frequencyPenalty = null;
    /**
     * @var bool|null Whether to return log probabilities.
     */
    protected ?bool $logprobs = null;
    /**
     * @var int|null Number of top log probabilities to return.
     */
    protected ?int $topLogprobs = null;
    /**
     * @var list<FunctionDeclaration>|null Function declarations available to the model.
     */
    protected ?array $functionDeclarations = null;
    /**
     * @var WebSearch|null Web search configuration for the model.
     */
    protected ?WebSearch $webSearch = null;
    /**
     * @var FileTypeEnum|null Output file type.
     */
    protected ?FileTypeEnum $outputFileType = null;
    /**
     * @var string|null Output MIME type.
     */
    protected ?string $outputMimeType = null;
    /**
     * @var array<string, mixed>|null Output schema (JSON schema).
     */
    protected ?array $outputSchema = null;
    /**
     * @var MediaOrientationEnum|null Output media orientation.
     */
    protected ?MediaOrientationEnum $outputMediaOrientation = null;
    /**
     * @var string|null Output media aspect ratio (e.g. 3:2, 16:9).
     */
    protected ?string $outputMediaAspectRatio = null;
    /**
     * @var string|null Output speech voice.
     */
    protected ?string $outputSpeechVoice = null;
    /**
     * @var array<string, mixed> Custom provider-specific options.
     */
    protected array $customOptions = [];
    /**
     * Creates a deep clone of this configuration.
     *
     * Clones nested objects (functionDeclarations, webSearch) to ensure
     * the cloned configuration is independent of the original.
     * Enum value objects (outputModalities, outputFileType, outputMediaOrientation)
     * are intentionally shared as they are immutable.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        // Deep clone function declarations if set
        if ($this->functionDeclarations !== null) {
            $clonedDeclarations = [];
            foreach ($this->functionDeclarations as $declaration) {
                $clonedDeclarations[] = clone $declaration;
            }
            $this->functionDeclarations = $clonedDeclarations;
        }
        // Clone web search if set
        if ($this->webSearch !== null) {
            $this->webSearch = clone $this->webSearch;
        }
        // Note: Enum value objects (outputModalities, outputFileType, outputMediaOrientation)
        // are immutable and can be safely shared.
    }
    /**
     * Sets the output modalities.
     *
     * @since 0.1.0
     *
     * @param list<ModalityEnum> $outputModalities The output modalities.
     *
     * @throws InvalidArgumentException If the array is not a list.
     */
    public function setOutputModalities(array $outputModalities): void
    {
        if (!array_is_list($outputModalities)) {
            throw new InvalidArgumentException('Output modalities must be a list array.');
        }
        $this->outputModalities = $outputModalities;
    }
    /**
     * Gets the output modalities.
     *
     * @since 0.1.0
     *
     * @return list<ModalityEnum>|null The output modalities.
     */
    public function getOutputModalities(): ?array
    {
        return $this->outputModalities;
    }
    /**
     * Sets the system instruction.
     *
     * @since 0.1.0
     *
     * @param string $systemInstruction The system instruction.
     */
    public function setSystemInstruction(string $systemInstruction): void
    {
        $this->systemInstruction = $systemInstruction;
    }
    /**
     * Gets the system instruction.
     *
     * @since 0.1.0
     *
     * @return string|null The system instruction.
     */
    public function getSystemInstruction(): ?string
    {
        return $this->systemInstruction;
    }
    /**
     * Sets the candidate count.
     *
     * @since 0.1.0
     *
     * @param int $candidateCount The candidate count.
     */
    public function setCandidateCount(int $candidateCount): void
    {
        $this->candidateCount = $candidateCount;
    }
    /**
     * Gets the candidate count.
     *
     * @since 0.1.0
     *
     * @return int|null The candidate count.
     */
    public function getCandidateCount(): ?int
    {
        return $this->candidateCount;
    }
    /**
     * Sets the maximum tokens.
     *
     * @since 0.1.0
     *
     * @param int $maxTokens The maximum tokens.
     */
    public function setMaxTokens(int $maxTokens): void
    {
        $this->maxTokens = $maxTokens;
    }
    /**
     * Gets the maximum tokens.
     *
     * @since 0.1.0
     *
     * @return int|null The maximum tokens.
     */
    public function getMaxTokens(): ?int
    {
        return $this->maxTokens;
    }
    /**
     * Sets the temperature.
     *
     * @since 0.1.0
     *
     * @param float $temperature The temperature.
     */
    public function setTemperature(float $temperature): void
    {
        $this->temperature = $temperature;
    }
    /**
     * Gets the temperature.
     *
     * @since 0.1.0
     *
     * @return float|null The temperature.
     */
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }
    /**
     * Sets the top-p parameter.
     *
     * @since 0.1.0
     *
     * @param float $topP The top-p parameter.
     */
    public function setTopP(float $topP): void
    {
        $this->topP = $topP;
    }
    /**
     * Gets the top-p parameter.
     *
     * @since 0.1.0
     *
     * @return float|null The top-p parameter.
     */
    public function getTopP(): ?float
    {
        return $this->topP;
    }
    /**
     * Sets the top-k parameter.
     *
     * @since 0.1.0
     *
     * @param int $topK The top-k parameter.
     */
    public function setTopK(int $topK): void
    {
        $this->topK = $topK;
    }
    /**
     * Gets the top-k parameter.
     *
     * @since 0.1.0
     *
     * @return int|null The top-k parameter.
     */
    public function getTopK(): ?int
    {
        return $this->topK;
    }
    /**
     * Sets the stop sequences.
     *
     * @since 0.1.0
     *
     * @param list<string> $stopSequences The stop sequences.
     *
     * @throws InvalidArgumentException If the array is not a list.
     */
    public function setStopSequences(array $stopSequences): void
    {
        if (!array_is_list($stopSequences)) {
            throw new InvalidArgumentException('Stop sequences must be a list array.');
        }
        $this->stopSequences = $stopSequences;
    }
    /**
     * Gets the stop sequences.
     *
     * @since 0.1.0
     *
     * @return list<string>|null The stop sequences.
     */
    public function getStopSequences(): ?array
    {
        return $this->stopSequences;
    }
    /**
     * Sets the presence penalty.
     *
     * @since 0.1.0
     *
     * @param float $presencePenalty The presence penalty.
     */
    public function setPresencePenalty(float $presencePenalty): void
    {
        $this->presencePenalty = $presencePenalty;
    }
    /**
     * Gets the presence penalty.
     *
     * @since 0.1.0
     *
     * @return float|null The presence penalty.
     */
    public function getPresencePenalty(): ?float
    {
        return $this->presencePenalty;
    }
    /**
     * Sets the frequency penalty.
     *
     * @since 0.1.0
     *
     * @param float $frequencyPenalty The frequency penalty.
     */
    public function setFrequencyPenalty(float $frequencyPenalty): void
    {
        $this->frequencyPenalty = $frequencyPenalty;
    }
    /**
     * Gets the frequency penalty.
     *
     * @since 0.1.0
     *
     * @return float|null The frequency penalty.
     */
    public function getFrequencyPenalty(): ?float
    {
        return $this->frequencyPenalty;
    }
    /**
     * Sets whether to return log probabilities.
     *
     * @since 0.1.0
     *
     * @param bool $logprobs Whether to return log probabilities.
     */
    public function setLogprobs(bool $logprobs): void
    {
        $this->logprobs = $logprobs;
    }
    /**
     * Gets whether to return log probabilities.
     *
     * @since 0.1.0
     *
     * @return bool|null Whether to return log probabilities.
     */
    public function getLogprobs(): ?bool
    {
        return $this->logprobs;
    }
    /**
     * Sets the number of top log probabilities to return.
     *
     * @since 0.1.0
     *
     * @param int $topLogprobs The number of top log probabilities.
     */
    public function setTopLogprobs(int $topLogprobs): void
    {
        $this->topLogprobs = $topLogprobs;
    }
    /**
     * Gets the number of top log probabilities to return.
     *
     * @since 0.1.0
     *
     * @return int|null The number of top log probabilities.
     */
    public function getTopLogprobs(): ?int
    {
        return $this->topLogprobs;
    }
    /**
     * Sets the function declarations.
     *
     * @since 0.1.0
     *
     * @param list<FunctionDeclaration> $functionDeclarations The function declarations.
     *
     * @throws InvalidArgumentException If the array is not a list.
     */
    public function setFunctionDeclarations(array $functionDeclarations): void
    {
        if (!array_is_list($functionDeclarations)) {
            throw new InvalidArgumentException('Function declarations must be a list array.');
        }
        $this->functionDeclarations = $functionDeclarations;
    }
    /**
     * Gets the function declarations.
     *
     * @since 0.1.0
     *
     * @return list<FunctionDeclaration>|null The function declarations.
     */
    public function getFunctionDeclarations(): ?array
    {
        return $this->functionDeclarations;
    }
    /**
     * Sets the web search configuration.
     *
     * @since 0.1.0
     *
     * @param WebSearch $webSearch The web search configuration.
     */
    public function setWebSearch(WebSearch $webSearch): void
    {
        $this->webSearch = $webSearch;
    }
    /**
     * Gets the web search configuration.
     *
     * @since 0.1.0
     *
     * @return WebSearch|null The web search configuration.
     */
    public function getWebSearch(): ?WebSearch
    {
        return $this->webSearch;
    }
    /**
     * Sets the output file type.
     *
     * @since 0.1.0
     *
     * @param FileTypeEnum $outputFileType The output file type.
     */
    public function setOutputFileType(FileTypeEnum $outputFileType): void
    {
        $this->outputFileType = $outputFileType;
    }
    /**
     * Gets the output file type.
     *
     * @since 0.1.0
     *
     * @return FileTypeEnum|null The output file type.
     */
    public function getOutputFileType(): ?FileTypeEnum
    {
        return $this->outputFileType;
    }
    /**
     * Sets the output MIME type.
     *
     * @since 0.1.0
     *
     * @param string $outputMimeType The output MIME type.
     */
    public function setOutputMimeType(string $outputMimeType): void
    {
        $this->outputMimeType = $outputMimeType;
    }
    /**
     * Gets the output MIME type.
     *
     * @since 0.1.0
     *
     * @return string|null The output MIME type.
     */
    public function getOutputMimeType(): ?string
    {
        return $this->outputMimeType;
    }
    /**
     * Sets the output schema.
     *
     * When setting an output schema, this method automatically sets
     * the output MIME type to "application/json" if not already set.
     *
     * @since 0.1.0
     *
     * @param array<string, mixed> $outputSchema The output schema (JSON schema).
     */
    public function setOutputSchema(array $outputSchema): void
    {
        $this->outputSchema = $outputSchema;
        // Automatically set outputMimeType to application/json when schema is provided
        if ($this->outputMimeType === null) {
            $this->outputMimeType = 'application/json';
        }
    }
    /**
     * Gets the output schema.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed>|null The output schema.
     */
    public function getOutputSchema(): ?array
    {
        return $this->outputSchema;
    }
    /**
     * Sets the output media orientation.
     *
     * @since 0.1.0
     *
     * @param MediaOrientationEnum $outputMediaOrientation The output media orientation.
     */
    public function setOutputMediaOrientation(MediaOrientationEnum $outputMediaOrientation): void
    {
        if ($this->outputMediaAspectRatio) {
            $this->validateMediaOrientationAspectRatioCompatibility($outputMediaOrientation, $this->outputMediaAspectRatio);
        }
        $this->outputMediaOrientation = $outputMediaOrientation;
    }
    /**
     * Gets the output media orientation.
     *
     * @since 0.1.0
     *
     * @return MediaOrientationEnum|null The output media orientation.
     */
    public function getOutputMediaOrientation(): ?MediaOrientationEnum
    {
        return $this->outputMediaOrientation;
    }
    /**
     * Sets the output media aspect ratio.
     *
     * If set, this supersedes the output media orientation, as it is a more specific configuration.
     *
     * @since 0.1.0
     *
     * @param string $outputMediaAspectRatio The output media aspect ratio (e.g. 3:2, 16:9).
     */
    public function setOutputMediaAspectRatio(string $outputMediaAspectRatio): void
    {
        if (!preg_match('/^\d+:\d+$/', $outputMediaAspectRatio)) {
            throw new InvalidArgumentException('Output media aspect ratio must be in the format "width:height" (e.g. 3:2, 16:9).');
        }
        if ($this->outputMediaOrientation) {
            $this->validateMediaOrientationAspectRatioCompatibility($this->outputMediaOrientation, $outputMediaAspectRatio);
        }
        $this->outputMediaAspectRatio = $outputMediaAspectRatio;
    }
    /**
     * Gets the output media aspect ratio.
     *
     * @since 0.1.0
     *
     * @return string|null The output media aspect ratio (e.g. 3:2, 16:9).
     */
    public function getOutputMediaAspectRatio(): ?string
    {
        return $this->outputMediaAspectRatio;
    }
    /**
     * Validates that the given media orientation and aspect ratio values do not conflict with each other.
     *
     * @since 0.4.0
     *
     * @param MediaOrientationEnum $orientation The desired media orientation.
     * @param string $aspectRatio The desired media aspect ratio.
     */
    protected function validateMediaOrientationAspectRatioCompatibility(MediaOrientationEnum $orientation, string $aspectRatio): void
    {
        $aspectRatioParts = explode(':', $aspectRatio);
        if ($orientation->isSquare() && $aspectRatioParts[0] !== $aspectRatioParts[1]) {
            throw new InvalidArgumentException('The aspect ratio "' . $aspectRatio . '" is not compatible with the square orientation.');
        }
        if ($orientation->isLandscape() && $aspectRatioParts[0] <= $aspectRatioParts[1]) {
            throw new InvalidArgumentException('The aspect ratio "' . $aspectRatio . '" is not compatible with the landscape orientation.');
        }
        if ($orientation->isPortrait() && $aspectRatioParts[0] >= $aspectRatioParts[1]) {
            throw new InvalidArgumentException('The aspect ratio "' . $aspectRatio . '" is not compatible with the portrait orientation.');
        }
    }
    /**
     * Sets the output speech voice.
     *
     * @since 0.1.0
     *
     * @param string $outputSpeechVoice The output speech voice.
     */
    public function setOutputSpeechVoice(string $outputSpeechVoice): void
    {
        $this->outputSpeechVoice = $outputSpeechVoice;
    }
    /**
     * Gets the output speech voice.
     *
     * @since 0.1.0
     *
     * @return string|null The output speech voice.
     */
    public function getOutputSpeechVoice(): ?string
    {
        return $this->outputSpeechVoice;
    }
    /**
     * Sets a single custom option.
     *
     * @since 0.1.0
     *
     * @param string $key   The option key.
     * @param mixed  $value The option value.
     */
    public function setCustomOption(string $key, $value): void
    {
        $this->customOptions[$key] = $value;
    }
    /**
     * Sets the custom options.
     *
     * @since 0.1.0
     *
     * @param array<string, mixed> $customOptions The custom options.
     */
    public function setCustomOptions(array $customOptions): void
    {
        $this->customOptions = $customOptions;
    }
    /**
     * Gets the custom options.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed> The custom options.
     */
    public function getCustomOptions(): array
    {
        return $this->customOptions;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_OUTPUT_MODALITIES => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => ModalityEnum::getValues()], 'description' => 'Output modalities for the model.'], self::KEY_SYSTEM_INSTRUCTION => ['type' => 'string', 'description' => 'System instruction for the model.'], self::KEY_CANDIDATE_COUNT => ['type' => 'integer', 'minimum' => 1, 'description' => 'Number of response candidates to generate.'], self::KEY_MAX_TOKENS => ['type' => 'integer', 'minimum' => 1, 'description' => 'Maximum number of tokens to generate.'], self::KEY_TEMPERATURE => ['type' => 'number', 'minimum' => 0.0, 'maximum' => 2.0, 'description' => 'Temperature for randomness.'], self::KEY_TOP_P => ['type' => 'number', 'minimum' => 0.0, 'maximum' => 1.0, 'description' => 'Top-p nucleus sampling parameter.'], self::KEY_TOP_K => ['type' => 'integer', 'minimum' => 1, 'description' => 'Top-k sampling parameter.'], self::KEY_STOP_SEQUENCES => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Stop sequences.'], self::KEY_PRESENCE_PENALTY => ['type' => 'number', 'description' => 'Presence penalty for reducing repetition.'], self::KEY_FREQUENCY_PENALTY => ['type' => 'number', 'description' => 'Frequency penalty for reducing repetition.'], self::KEY_LOGPROBS => ['type' => 'boolean', 'description' => 'Whether to return log probabilities.'], self::KEY_TOP_LOGPROBS => ['type' => 'integer', 'minimum' => 1, 'description' => 'Number of top log probabilities to return.'], self::KEY_FUNCTION_DECLARATIONS => ['type' => 'array', 'items' => FunctionDeclaration::getJsonSchema(), 'description' => 'Function declarations available to the model.'], self::KEY_WEB_SEARCH => WebSearch::getJsonSchema(), self::KEY_OUTPUT_FILE_TYPE => ['type' => 'string', 'enum' => FileTypeEnum::getValues(), 'description' => 'Output file type.'], self::KEY_OUTPUT_MIME_TYPE => ['type' => 'string', 'description' => 'Output MIME type.'], self::KEY_OUTPUT_SCHEMA => ['type' => 'object', 'additionalProperties' => \true, 'description' => 'Output schema (JSON schema).'], self::KEY_OUTPUT_MEDIA_ORIENTATION => ['type' => 'string', 'enum' => MediaOrientationEnum::getValues(), 'description' => 'Output media orientation.'], self::KEY_OUTPUT_MEDIA_ASPECT_RATIO => ['type' => 'string', 'pattern' => '^\d+:\d+$', 'description' => 'Output media aspect ratio.'], self::KEY_OUTPUT_SPEECH_VOICE => ['type' => 'string', 'description' => 'Output speech voice.'], self::KEY_CUSTOM_OPTIONS => ['type' => 'object', 'additionalProperties' => \true, 'description' => 'Custom provider-specific options.']], 'additionalProperties' => \false];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return ModelConfigArrayShape
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->outputModalities !== null) {
            $data[self::KEY_OUTPUT_MODALITIES] = array_map(static function (ModalityEnum $modality): string {
                return $modality->value;
            }, $this->outputModalities);
        }
        if ($this->systemInstruction !== null) {
            $data[self::KEY_SYSTEM_INSTRUCTION] = $this->systemInstruction;
        }
        if ($this->candidateCount !== null) {
            $data[self::KEY_CANDIDATE_COUNT] = $this->candidateCount;
        }
        if ($this->maxTokens !== null) {
            $data[self::KEY_MAX_TOKENS] = $this->maxTokens;
        }
        if ($this->temperature !== null) {
            $data[self::KEY_TEMPERATURE] = $this->temperature;
        }
        if ($this->topP !== null) {
            $data[self::KEY_TOP_P] = $this->topP;
        }
        if ($this->topK !== null) {
            $data[self::KEY_TOP_K] = $this->topK;
        }
        if ($this->stopSequences !== null) {
            $data[self::KEY_STOP_SEQUENCES] = $this->stopSequences;
        }
        if ($this->presencePenalty !== null) {
            $data[self::KEY_PRESENCE_PENALTY] = $this->presencePenalty;
        }
        if ($this->frequencyPenalty !== null) {
            $data[self::KEY_FREQUENCY_PENALTY] = $this->frequencyPenalty;
        }
        if ($this->logprobs !== null) {
            $data[self::KEY_LOGPROBS] = $this->logprobs;
        }
        if ($this->topLogprobs !== null) {
            $data[self::KEY_TOP_LOGPROBS] = $this->topLogprobs;
        }
        if ($this->functionDeclarations !== null) {
            $data[self::KEY_FUNCTION_DECLARATIONS] = array_map(static function (FunctionDeclaration $functionDeclaration): array {
                return $functionDeclaration->toArray();
            }, $this->functionDeclarations);
        }
        if ($this->webSearch !== null) {
            $data[self::KEY_WEB_SEARCH] = $this->webSearch->toArray();
        }
        if ($this->outputFileType !== null) {
            $data[self::KEY_OUTPUT_FILE_TYPE] = $this->outputFileType->value;
        }
        if ($this->outputMimeType !== null) {
            $data[self::KEY_OUTPUT_MIME_TYPE] = $this->outputMimeType;
        }
        if ($this->outputSchema !== null) {
            $data[self::KEY_OUTPUT_SCHEMA] = $this->outputSchema;
        }
        if ($this->outputMediaOrientation !== null) {
            $data[self::KEY_OUTPUT_MEDIA_ORIENTATION] = $this->outputMediaOrientation->value;
        }
        if ($this->outputMediaAspectRatio !== null) {
            $data[self::KEY_OUTPUT_MEDIA_ASPECT_RATIO] = $this->outputMediaAspectRatio;
        }
        if ($this->outputSpeechVoice !== null) {
            $data[self::KEY_OUTPUT_SPEECH_VOICE] = $this->outputSpeechVoice;
        }
        if (!empty($this->customOptions)) {
            $data[self::KEY_CUSTOM_OPTIONS] = $this->customOptions;
        }
        return $data;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        $config = new self();
        if (isset($array[self::KEY_OUTPUT_MODALITIES])) {
            $config->setOutputModalities(array_map(static fn(string $modality): ModalityEnum => ModalityEnum::from($modality), $array[self::KEY_OUTPUT_MODALITIES]));
        }
        if (isset($array[self::KEY_SYSTEM_INSTRUCTION])) {
            $config->setSystemInstruction($array[self::KEY_SYSTEM_INSTRUCTION]);
        }
        if (isset($array[self::KEY_CANDIDATE_COUNT])) {
            $config->setCandidateCount($array[self::KEY_CANDIDATE_COUNT]);
        }
        if (isset($array[self::KEY_MAX_TOKENS])) {
            $config->setMaxTokens($array[self::KEY_MAX_TOKENS]);
        }
        if (isset($array[self::KEY_TEMPERATURE])) {
            $config->setTemperature($array[self::KEY_TEMPERATURE]);
        }
        if (isset($array[self::KEY_TOP_P])) {
            $config->setTopP($array[self::KEY_TOP_P]);
        }
        if (isset($array[self::KEY_TOP_K])) {
            $config->setTopK($array[self::KEY_TOP_K]);
        }
        if (isset($array[self::KEY_STOP_SEQUENCES])) {
            $config->setStopSequences($array[self::KEY_STOP_SEQUENCES]);
        }
        if (isset($array[self::KEY_PRESENCE_PENALTY])) {
            $config->setPresencePenalty($array[self::KEY_PRESENCE_PENALTY]);
        }
        if (isset($array[self::KEY_FREQUENCY_PENALTY])) {
            $config->setFrequencyPenalty($array[self::KEY_FREQUENCY_PENALTY]);
        }
        if (isset($array[self::KEY_LOGPROBS])) {
            $config->setLogprobs($array[self::KEY_LOGPROBS]);
        }
        if (isset($array[self::KEY_TOP_LOGPROBS])) {
            $config->setTopLogprobs($array[self::KEY_TOP_LOGPROBS]);
        }
        if (isset($array[self::KEY_FUNCTION_DECLARATIONS])) {
            $config->setFunctionDeclarations(array_map(static function (array $functionDeclarationData): FunctionDeclaration {
                return FunctionDeclaration::fromArray($functionDeclarationData);
            }, $array[self::KEY_FUNCTION_DECLARATIONS]));
        }
        if (isset($array[self::KEY_WEB_SEARCH])) {
            $config->setWebSearch(WebSearch::fromArray($array[self::KEY_WEB_SEARCH]));
        }
        if (isset($array[self::KEY_OUTPUT_FILE_TYPE])) {
            $config->setOutputFileType(FileTypeEnum::from($array[self::KEY_OUTPUT_FILE_TYPE]));
        }
        if (isset($array[self::KEY_OUTPUT_MIME_TYPE])) {
            $config->setOutputMimeType($array[self::KEY_OUTPUT_MIME_TYPE]);
        }
        if (isset($array[self::KEY_OUTPUT_SCHEMA])) {
            $config->setOutputSchema($array[self::KEY_OUTPUT_SCHEMA]);
        }
        if (isset($array[self::KEY_OUTPUT_MEDIA_ORIENTATION])) {
            $config->setOutputMediaOrientation(MediaOrientationEnum::from($array[self::KEY_OUTPUT_MEDIA_ORIENTATION]));
        }
        if (isset($array[self::KEY_OUTPUT_MEDIA_ASPECT_RATIO])) {
            $config->setOutputMediaAspectRatio($array[self::KEY_OUTPUT_MEDIA_ASPECT_RATIO]);
        }
        if (isset($array[self::KEY_OUTPUT_SPEECH_VOICE])) {
            $config->setOutputSpeechVoice($array[self::KEY_OUTPUT_SPEECH_VOICE]);
        }
        if (isset($array[self::KEY_CUSTOM_OPTIONS])) {
            $config->setCustomOptions($array[self::KEY_CUSTOM_OPTIONS]);
        }
        return $config;
    }
}
