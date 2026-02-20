<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\ApiBasedImplementation;

use Exception;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelConfig;
use WordPress\AiClient\Providers\Models\TextGeneration\Contracts\TextGenerationModelInterface;
/**
 * Class to check availability for an API-based provider via a test request to the endpoint to generate text.
 *
 * This class should be used for cloud-based providers that do not offer a model listing endpoint, but do offer a
 * text generation endpoint which requires authentication. A minimal request to this endpoint is used to determine
 * if the provider is properly configured with valid credentials.
 *
 * @since 0.1.0
 */
class GenerateTextApiBasedProviderAvailability implements ProviderAvailabilityInterface
{
    /**
     * @var ModelInterface&TextGenerationModelInterface The model to use for checking availability.
     */
    private ModelInterface $model;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param ModelInterface $model The model to use for checking availability.
     */
    public function __construct(ModelInterface $model)
    {
        if (!$model instanceof TextGenerationModelInterface) {
            throw new Exception('The model class to check provider availability must implement TextGenerationModelInterface.');
        }
        $this->model = $model;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function isConfigured(): bool
    {
        // Set config to use as few resources as possible for the test.
        $modelConfig = ModelConfig::fromArray([ModelConfig::KEY_MAX_TOKENS => 1]);
        $this->model->setConfig($modelConfig);
        try {
            // Attempt to generate text to check if the provider is available.
            $this->model->generateTextResult([new Message(MessageRoleEnum::user(), [new MessagePart('a')])]);
            return \true;
        } catch (Exception $e) {
            // If an exception occurs, the provider is not available.
            return \false;
        }
    }
}
