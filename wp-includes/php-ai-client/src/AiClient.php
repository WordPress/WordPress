<?php

declare (strict_types=1);
namespace WordPress\AiClient;

use WordPress\AiClientDependencies\Psr\EventDispatcher\EventDispatcherInterface;
use WordPress\AiClientDependencies\Psr\SimpleCache\CacheInterface;
use WordPress\AiClient\Builders\PromptBuilder;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\Contracts\ProviderInterface;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\ProviderRegistry;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
/**
 * Main AI Client class providing both fluent and traditional APIs for AI operations.
 *
 * This class serves as the primary entry point for AI operations, offering:
 * - Fluent API for easy-to-read chained method calls
 * - Traditional API for array-based configuration (WordPress style)
 * - Integration with provider registry for model discovery
 * - Support for three model specification approaches
 *
 * All model requirements analysis and capability matching is handled
 * automatically by the PromptBuilder, which provides intelligent model
 * discovery based on prompt content and configuration.
 *
 * ## Model Specification Approaches
 *
 * ### 1. Specific Model Instance
 * Use a specific ModelInterface instance when you know exactly which model to use:
 * ```php
 * $model = $registry->getProvider('openai')->getModel('gpt-4');
 * $result = AiClient::generateTextResult('What is PHP?', $model);
 * ```
 *
 * ### 2. ModelConfig for Auto-Discovery
 * Use ModelConfig to specify requirements and let the system discover the best model:
 * ```php
 * $config = new ModelConfig();
 * $config->setTemperature(0.7);
 * $config->setMaxTokens(150);
 *
 * $result = AiClient::generateTextResult('What is PHP?', $config);
 * ```
 *
 * ### 3. Automatic Discovery (Default)
 * Pass null or omit the parameter for intelligent model discovery based on prompt content:
 * ```php
 * // System analyzes prompt and selects appropriate model automatically
 * $result = AiClient::generateTextResult('What is PHP?');
 * $imageResult = AiClient::generateImageResult('A sunset over mountains');
 * ```
 *
 * ## Fluent API Examples
 * ```php
 * // Fluent API with automatic model discovery
 * $result = AiClient::prompt('Generate an image of a sunset')
 *     ->usingTemperature(0.7)
 *     ->generateImageResult();
 *
 * // Fluent API with specific model
 * $result = AiClient::prompt('What is PHP?')
 *     ->usingModel($specificModel)
 *     ->usingTemperature(0.5)
 *     ->generateTextResult();
 *
 * // Fluent API with model configuration
 * $result = AiClient::prompt('Explain quantum physics')
 *     ->usingModelConfig($config)
 *     ->generateTextResult();
 * ```
 *
 * @since 0.1.0
 *
 * @phpstan-import-type Prompt from PromptBuilder
 *
 * phpcs:ignore Generic.Files.LineLength.TooLong
 */
class AiClient
{
    /**
     * @var string The version of the AI Client.
     */
    public const VERSION = '1.1.0';
    /**
     * @var ProviderRegistry|null The default provider registry instance.
     */
    private static ?ProviderRegistry $defaultRegistry = null;
    /**
     * @var EventDispatcherInterface|null The event dispatcher for prompt lifecycle events.
     */
    private static ?EventDispatcherInterface $eventDispatcher = null;
    /**
     * @var CacheInterface|null The PSR-16 cache for storing and retrieving cached data.
     */
    private static ?CacheInterface $cache = null;
    /**
     * Gets the default provider registry instance.
     *
     * @since 0.1.0
     *
     * @return ProviderRegistry The default provider registry.
     */
    public static function defaultRegistry(): ProviderRegistry
    {
        if (self::$defaultRegistry === null) {
            self::$defaultRegistry = new ProviderRegistry();
        }
        return self::$defaultRegistry;
    }
    /**
     * Sets the event dispatcher for prompt lifecycle events.
     *
     * The event dispatcher will be used to dispatch BeforeGenerateResultEvent and
     * AfterGenerateResultEvent during prompt generation.
     *
     * @since 0.4.0
     *
     * @param EventDispatcherInterface|null $dispatcher The event dispatcher, or null to disable.
     * @return void
     */
    public static function setEventDispatcher(?EventDispatcherInterface $dispatcher): void
    {
        self::$eventDispatcher = $dispatcher;
    }
    /**
     * Gets the event dispatcher for prompt lifecycle events.
     *
     * @since 0.4.0
     *
     * @return EventDispatcherInterface|null The event dispatcher, or null if not set.
     */
    public static function getEventDispatcher(): ?EventDispatcherInterface
    {
        return self::$eventDispatcher;
    }
    /**
     * Sets the PSR-16 cache for storing and retrieving cached data.
     *
     * The cache can be used to store AI responses and other data to avoid
     * redundant API calls and improve performance.
     *
     * @since 0.4.0
     *
     * @param CacheInterface|null $cache The PSR-16 cache instance, or null to disable caching.
     * @return void
     */
    public static function setCache(?CacheInterface $cache): void
    {
        self::$cache = $cache;
    }
    /**
     * Gets the PSR-16 cache instance.
     *
     * @since 0.4.0
     *
     * @return CacheInterface|null The cache instance, or null if not set.
     */
    public static function getCache(): ?CacheInterface
    {
        return self::$cache;
    }
    /**
     * Checks if a provider is configured and available for use.
     *
     * Supports multiple input formats for developer convenience:
     * - ProviderAvailabilityInterface: Direct availability check
     * - string (provider ID): e.g., AiClient::isConfigured('openai')
     * - string (class name): e.g., AiClient::isConfigured(OpenAiProvider::class)
     *
     * When using string input, this method leverages the ProviderRegistry's centralized
     * dependency management, ensuring HttpTransporter and authentication are properly
     * injected into availability instances.
     *
     * @since 0.1.0
     * @since 0.2.0 Now supports being passed a provider ID or class name.
     *
     * @param ProviderAvailabilityInterface|string|class-string<ProviderInterface> $availabilityOrIdOrClassName
     *        The provider availability instance, provider ID, or provider class name.
     * @return bool True if the provider is configured and available, false otherwise.
     */
    public static function isConfigured($availabilityOrIdOrClassName): bool
    {
        // Handle direct ProviderAvailabilityInterface (backward compatibility)
        if ($availabilityOrIdOrClassName instanceof ProviderAvailabilityInterface) {
            return $availabilityOrIdOrClassName->isConfigured();
        }
        // Handle string input (provider ID or class name) via registry
        if (is_string($availabilityOrIdOrClassName)) {
            return self::defaultRegistry()->isProviderConfigured($availabilityOrIdOrClassName);
        }
        throw new \InvalidArgumentException('Parameter must be a ProviderAvailabilityInterface instance, provider ID string, or provider class name. ' . sprintf('Received: %s', is_object($availabilityOrIdOrClassName) ? get_class($availabilityOrIdOrClassName) : gettype($availabilityOrIdOrClassName)));
    }
    /**
     * Creates a new prompt builder for fluent API usage.
     *
     * Returns a PromptBuilder instance configured with the specified or default registry.
     * The traditional API methods in this class delegate to PromptBuilder
     * for all generation logic.
     *
     * @since 0.1.0
     *
     * @param Prompt $prompt Optional initial prompt content.
     * @param ProviderRegistry|null $registry Optional custom registry. If null, uses default.
     * @return PromptBuilder The prompt builder instance.
     */
    public static function prompt($prompt = null, ?ProviderRegistry $registry = null): PromptBuilder
    {
        return new PromptBuilder($registry ?? self::defaultRegistry(), $prompt, self::$eventDispatcher);
    }
    /**
     * Generates content using a unified API that automatically detects model capabilities.
     *
     * When no model is provided, this method delegates to PromptBuilder for intelligent
     * model discovery based on prompt content and configuration. When a model is provided,
     * it infers the capability from the model's interfaces and delegates to the capability-based method.
     *
     * @since 0.1.0
     *
     * @param Prompt $prompt The prompt content.
     * @param ModelInterface|ModelConfig $modelOrConfig Specific model to use, or model configuration
     *                                                  for auto-discovery.
     * @param ProviderRegistry|null $registry Optional custom registry. If null, uses default.
     * @return GenerativeAiResult The generation result.
     *
     * @throws \InvalidArgumentException If the provided model doesn't support any known generation type.
     * @throws \RuntimeException If no suitable model can be found for the prompt.
     */
    public static function generateResult($prompt, $modelOrConfig, ?ProviderRegistry $registry = null): GenerativeAiResult
    {
        self::validateModelOrConfigParameter($modelOrConfig);
        return self::getConfiguredPromptBuilder($prompt, $modelOrConfig, $registry)->generateResult();
    }
    /**
     * Generates text using the traditional API approach.
     *
     * @since 0.1.0
     *
     * @param Prompt $prompt The prompt content.
     * @param ModelInterface|ModelConfig|null $modelOrConfig Optional specific model to use,
     *                                                        or model configuration for auto-discovery,
     *                                                        or null for defaults.
     * @param ProviderRegistry|null $registry Optional custom registry. If null, uses default.
     * @return GenerativeAiResult The generation result.
     *
     * @throws \InvalidArgumentException If the prompt format is invalid.
     * @throws \RuntimeException If no suitable model is found.
     */
    public static function generateTextResult($prompt, $modelOrConfig = null, ?ProviderRegistry $registry = null): GenerativeAiResult
    {
        self::validateModelOrConfigParameter($modelOrConfig);
        return self::getConfiguredPromptBuilder($prompt, $modelOrConfig, $registry)->generateTextResult();
    }
    /**
     * Generates an image using the traditional API approach.
     *
     * @since 0.1.0
     *
     * @param Prompt $prompt The prompt content.
     * @param ModelInterface|ModelConfig|null $modelOrConfig Optional specific model to use,
     *                                                        or model configuration for auto-discovery,
     *                                                        or null for defaults.
     * @param ProviderRegistry|null $registry Optional custom registry. If null, uses default.
     * @return GenerativeAiResult The generation result.
     *
     * @throws \InvalidArgumentException If the prompt format is invalid.
     * @throws \RuntimeException If no suitable model is found.
     */
    public static function generateImageResult($prompt, $modelOrConfig = null, ?ProviderRegistry $registry = null): GenerativeAiResult
    {
        self::validateModelOrConfigParameter($modelOrConfig);
        return self::getConfiguredPromptBuilder($prompt, $modelOrConfig, $registry)->generateImageResult();
    }
    /**
     * Converts text to speech using the traditional API approach.
     *
     * @since 0.1.0
     *
     * @param Prompt $prompt The prompt content.
     * @param ModelInterface|ModelConfig|null $modelOrConfig Optional specific model to use,
     *                                                        or model configuration for auto-discovery,
     *                                                        or null for defaults.
     * @param ProviderRegistry|null $registry Optional custom registry. If null, uses default.
     * @return GenerativeAiResult The generation result.
     *
     * @throws \InvalidArgumentException If the prompt format is invalid.
     * @throws \RuntimeException If no suitable model is found.
     */
    public static function convertTextToSpeechResult($prompt, $modelOrConfig = null, ?ProviderRegistry $registry = null): GenerativeAiResult
    {
        self::validateModelOrConfigParameter($modelOrConfig);
        return self::getConfiguredPromptBuilder($prompt, $modelOrConfig, $registry)->convertTextToSpeechResult();
    }
    /**
     * Generates speech using the traditional API approach.
     *
     * @since 0.1.0
     *
     * @param Prompt $prompt The prompt content.
     * @param ModelInterface|ModelConfig|null $modelOrConfig Optional specific model to use,
     *                                                        or model configuration for auto-discovery,
     *                                                        or null for defaults.
     * @param ProviderRegistry|null $registry Optional custom registry. If null, uses default.
     * @return GenerativeAiResult The generation result.
     *
     * @throws \InvalidArgumentException If the prompt format is invalid.
     * @throws \RuntimeException If no suitable model is found.
     */
    public static function generateSpeechResult($prompt, $modelOrConfig = null, ?ProviderRegistry $registry = null): GenerativeAiResult
    {
        self::validateModelOrConfigParameter($modelOrConfig);
        return self::getConfiguredPromptBuilder($prompt, $modelOrConfig, $registry)->generateSpeechResult();
    }
    /**
     * Creates a new message builder for fluent API usage.
     *
     * This method will be implemented once MessageBuilder is available.
     * MessageBuilder will provide a fluent interface for constructing complex
     * messages with multiple parts, attachments, and metadata.
     *
     * @since 0.1.0
     *
     * @param string|null $text Optional initial message text.
     * @return object MessageBuilder instance (type will be updated when MessageBuilder is available).
     *
     * @throws \RuntimeException When MessageBuilder is not yet available.
     */
    public static function message(?string $text = null)
    {
        throw new RuntimeException('MessageBuilder is not yet available. This method depends on builder infrastructure. ' . 'Use direct generation methods (generateTextResult, generateImageResult, etc.) for now.');
    }
    /**
     * Validates that parameter is ModelInterface, ModelConfig, or null.
     *
     * @param mixed $modelOrConfig The parameter to validate.
     * @return void
     * @throws \InvalidArgumentException If parameter is invalid type.
     */
    private static function validateModelOrConfigParameter($modelOrConfig): void
    {
        if ($modelOrConfig !== null && !$modelOrConfig instanceof ModelInterface && !$modelOrConfig instanceof ModelConfig) {
            throw new InvalidArgumentException('Parameter must be a ModelInterface instance (specific model), ' . 'ModelConfig instance (for auto-discovery), or null (default auto-discovery). ' . sprintf('Received: %s', is_object($modelOrConfig) ? get_class($modelOrConfig) : gettype($modelOrConfig)));
        }
    }
    /**
     * Configures PromptBuilder based on model/config parameter type.
     *
     * @param Prompt $prompt The prompt content.
     * @param ModelInterface|ModelConfig|null $modelOrConfig The model or config parameter.
     * @param ProviderRegistry|null $registry Optional custom registry to use.
     * @return PromptBuilder Configured prompt builder.
     */
    private static function getConfiguredPromptBuilder($prompt, $modelOrConfig, ?ProviderRegistry $registry = null): PromptBuilder
    {
        $builder = self::prompt($prompt, $registry);
        if ($modelOrConfig instanceof ModelInterface) {
            $builder->usingModel($modelOrConfig);
        } elseif ($modelOrConfig instanceof ModelConfig) {
            $builder->usingModelConfig($modelOrConfig);
        }
        // null case: use default model discovery
        return $builder;
    }
}
