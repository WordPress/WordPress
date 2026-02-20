<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;
/**
 * Represents requirements that implementing code has for AI model selection.
 *
 * This class defines the capabilities and options that a model must support
 * in order to be considered suitable for the implementing code's needs.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type RequiredOptionArrayShape from RequiredOption
 *
 * @phpstan-type ModelRequirementsArrayShape array{
 *     requiredCapabilities: list<string>,
 *     requiredOptions: list<RequiredOptionArrayShape>
 * }
 *
 * @extends AbstractDataTransferObject<ModelRequirementsArrayShape>
 */
class ModelRequirements extends AbstractDataTransferObject
{
    public const KEY_REQUIRED_CAPABILITIES = 'requiredCapabilities';
    public const KEY_REQUIRED_OPTIONS = 'requiredOptions';
    /**
     * @var list<CapabilityEnum> The capabilities that the model must support.
     */
    protected array $requiredCapabilities;
    /**
     * @var list<RequiredOption> The options that the model must support with specific values.
     */
    protected array $requiredOptions;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param list<CapabilityEnum> $requiredCapabilities The capabilities that the model must support.
     * @param list<RequiredOption> $requiredOptions The options that the model must support with specific values.
     *
     * @throws InvalidArgumentException If arrays are not lists.
     */
    public function __construct(array $requiredCapabilities, array $requiredOptions)
    {
        if (!array_is_list($requiredCapabilities)) {
            throw new InvalidArgumentException('Required capabilities must be a list array.');
        }
        if (!array_is_list($requiredOptions)) {
            throw new InvalidArgumentException('Required options must be a list array.');
        }
        $this->requiredCapabilities = $requiredCapabilities;
        $this->requiredOptions = $requiredOptions;
    }
    /**
     * Gets the capabilities that the model must support.
     *
     * @since 0.1.0
     *
     * @return list<CapabilityEnum> The required capabilities.
     */
    public function getRequiredCapabilities(): array
    {
        return $this->requiredCapabilities;
    }
    /**
     * Gets the options that the model must support with specific values.
     *
     * @since 0.1.0
     *
     * @return list<RequiredOption> The required options.
     */
    public function getRequiredOptions(): array
    {
        return $this->requiredOptions;
    }
    /**
     * Checks whether the given model metadata meets these requirements.
     *
     * @since 0.2.0
     *
     * @param ModelMetadata $metadata The model metadata to check against.
     * @return bool True if the model meets all requirements, false otherwise.
     */
    public function areMetBy(\WordPress\AiClient\Providers\Models\DTO\ModelMetadata $metadata): bool
    {
        // Create lookup maps for better performance (instead of nested foreach loops)
        $capabilitiesMap = [];
        foreach ($metadata->getSupportedCapabilities() as $capability) {
            $capabilitiesMap[$capability->value] = $capability;
        }
        $optionsMap = [];
        foreach ($metadata->getSupportedOptions() as $option) {
            $optionsMap[$option->getName()->value] = $option;
        }
        // Check if all required capabilities are supported using map lookup
        foreach ($this->requiredCapabilities as $requiredCapability) {
            if (!isset($capabilitiesMap[$requiredCapability->value])) {
                return \false;
            }
        }
        // Check if all required options are supported with the specified values
        foreach ($this->requiredOptions as $requiredOption) {
            // Use map lookup instead of linear search
            if (!isset($optionsMap[$requiredOption->getName()->value])) {
                return \false;
            }
            $supportedOption = $optionsMap[$requiredOption->getName()->value];
            // Check if the required value is supported by this option
            if (!$supportedOption->isSupportedValue($requiredOption->getValue())) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Creates ModelRequirements from prompt data and model configuration.
     *
     * @since 0.2.0
     *
     * @param CapabilityEnum $capability The capability the model must support.
     * @param list<Message> $messages The messages in the conversation.
     * @param ModelConfig $modelConfig The model configuration.
     * @return self The created requirements.
     */
    public static function fromPromptData(CapabilityEnum $capability, array $messages, \WordPress\AiClient\Providers\Models\DTO\ModelConfig $modelConfig): self
    {
        // Start with base capability
        $capabilities = [$capability];
        $inputModalities = [];
        // Check if we have chat history (multiple messages)
        if (count($messages) > 1) {
            $capabilities[] = CapabilityEnum::chatHistory();
        }
        // Analyze all messages to determine required input modalities
        $hasFunctionMessageParts = \false;
        foreach ($messages as $message) {
            foreach ($message->getParts() as $part) {
                // Check for text input
                if ($part->getType()->isText()) {
                    $inputModalities[] = ModalityEnum::text();
                }
                // Check for file inputs
                if ($part->getType()->isFile()) {
                    $file = $part->getFile();
                    if ($file !== null) {
                        if ($file->isImage()) {
                            $inputModalities[] = ModalityEnum::image();
                        } elseif ($file->isAudio()) {
                            $inputModalities[] = ModalityEnum::audio();
                        } elseif ($file->isVideo()) {
                            $inputModalities[] = ModalityEnum::video();
                        } elseif ($file->isDocument() || $file->isText()) {
                            $inputModalities[] = ModalityEnum::document();
                        }
                    }
                }
                // Check for function calls/responses (these might require special capabilities)
                if ($part->getType()->isFunctionCall() || $part->getType()->isFunctionResponse()) {
                    $hasFunctionMessageParts = \true;
                }
            }
        }
        // Convert ModelConfig to RequiredOptions
        $requiredOptions = self::toRequiredOptions($modelConfig);
        // Add additional options based on message analysis
        if ($hasFunctionMessageParts) {
            $requiredOptions = self::includeInRequiredOptions($requiredOptions, new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::functionDeclarations(), \true));
        }
        // Add input modalities if we have any inputs
        if (!empty($inputModalities)) {
            // Remove duplicates
            $inputModalities = array_unique($inputModalities, \SORT_REGULAR);
            $requiredOptions = self::includeInRequiredOptions($requiredOptions, new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::inputModalities(), array_values($inputModalities)));
        }
        // Step 6: Return new ModelRequirements
        return new self($capabilities, $requiredOptions);
    }
    /**
     * Converts ModelConfig to an array of RequiredOptions.
     *
     * @since 0.2.0
     *
     * @param ModelConfig $modelConfig The model configuration.
     * @return list<RequiredOption> The required options.
     */
    private static function toRequiredOptions(\WordPress\AiClient\Providers\Models\DTO\ModelConfig $modelConfig): array
    {
        $requiredOptions = [];
        // Map properties that have corresponding OptionEnum values
        if ($modelConfig->getOutputModalities() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::outputModalities(), $modelConfig->getOutputModalities());
        }
        if ($modelConfig->getSystemInstruction() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::systemInstruction(), $modelConfig->getSystemInstruction());
        }
        if ($modelConfig->getCandidateCount() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::candidateCount(), $modelConfig->getCandidateCount());
        }
        if ($modelConfig->getMaxTokens() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::maxTokens(), $modelConfig->getMaxTokens());
        }
        if ($modelConfig->getTemperature() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::temperature(), $modelConfig->getTemperature());
        }
        if ($modelConfig->getTopP() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::topP(), $modelConfig->getTopP());
        }
        if ($modelConfig->getTopK() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::topK(), $modelConfig->getTopK());
        }
        if ($modelConfig->getOutputMimeType() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::outputMimeType(), $modelConfig->getOutputMimeType());
        }
        if ($modelConfig->getOutputSchema() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::outputSchema(), $modelConfig->getOutputSchema());
        }
        // Handle properties without OptionEnum values as custom options
        if ($modelConfig->getStopSequences() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::stopSequences(), $modelConfig->getStopSequences());
        }
        if ($modelConfig->getPresencePenalty() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::presencePenalty(), $modelConfig->getPresencePenalty());
        }
        if ($modelConfig->getFrequencyPenalty() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::frequencyPenalty(), $modelConfig->getFrequencyPenalty());
        }
        if ($modelConfig->getLogprobs() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::logprobs(), $modelConfig->getLogprobs());
        }
        if ($modelConfig->getTopLogprobs() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::topLogprobs(), $modelConfig->getTopLogprobs());
        }
        if ($modelConfig->getFunctionDeclarations() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::functionDeclarations(), \true);
        }
        if ($modelConfig->getWebSearch() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::webSearch(), \true);
        }
        if ($modelConfig->getOutputFileType() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::outputFileType(), $modelConfig->getOutputFileType());
        }
        if ($modelConfig->getOutputMediaOrientation() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::outputMediaOrientation(), $modelConfig->getOutputMediaOrientation());
        }
        if ($modelConfig->getOutputMediaAspectRatio() !== null) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::outputMediaAspectRatio(), $modelConfig->getOutputMediaAspectRatio());
        }
        // Add custom options as individual RequiredOptions
        foreach ($modelConfig->getCustomOptions() as $key => $value) {
            $requiredOptions[] = new \WordPress\AiClient\Providers\Models\DTO\RequiredOption(OptionEnum::customOptions(), [$key => $value]);
        }
        return $requiredOptions;
    }
    /**
     * Includes a RequiredOption in the array, ensuring no duplicates based on option name.
     *
     * @since 0.2.0
     *
     * @param list<RequiredOption> $requiredOptions The existing required options.
     * @param RequiredOption $newOption The new option to include.
     * @return list<RequiredOption> The updated required options array.
     */
    private static function includeInRequiredOptions(array $requiredOptions, \WordPress\AiClient\Providers\Models\DTO\RequiredOption $newOption): array
    {
        // Check if we already have this option name
        foreach ($requiredOptions as $index => $existingOption) {
            if ($existingOption->getName()->equals($newOption->getName())) {
                // Replace existing option with new one
                $requiredOptions[$index] = $newOption;
                return $requiredOptions;
            }
        }
        // Option not found, add it
        $requiredOptions[] = $newOption;
        return $requiredOptions;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_REQUIRED_CAPABILITIES => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => CapabilityEnum::getValues()], 'description' => 'The capabilities that the model must support.'], self::KEY_REQUIRED_OPTIONS => ['type' => 'array', 'items' => \WordPress\AiClient\Providers\Models\DTO\RequiredOption::getJsonSchema(), 'description' => 'The options that the model must support with specific values.']], 'required' => [self::KEY_REQUIRED_CAPABILITIES, self::KEY_REQUIRED_OPTIONS]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return ModelRequirementsArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_REQUIRED_CAPABILITIES => array_map(static fn(CapabilityEnum $capability): string => $capability->value, $this->requiredCapabilities), self::KEY_REQUIRED_OPTIONS => array_map(static fn(\WordPress\AiClient\Providers\Models\DTO\RequiredOption $option): array => $option->toArray(), $this->requiredOptions)];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_REQUIRED_CAPABILITIES, self::KEY_REQUIRED_OPTIONS]);
        return new self(array_map(static fn(string $capability): CapabilityEnum => CapabilityEnum::from($capability), $array[self::KEY_REQUIRED_CAPABILITIES]), array_map(static fn(array $optionData): \WordPress\AiClient\Providers\Models\DTO\RequiredOption => \WordPress\AiClient\Providers\Models\DTO\RequiredOption::fromArray($optionData), $array[self::KEY_REQUIRED_OPTIONS]));
    }
}
