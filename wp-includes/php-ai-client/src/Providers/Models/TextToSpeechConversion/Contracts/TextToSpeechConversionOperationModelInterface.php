<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\TextToSpeechConversion\Contracts;

use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Operations\DTO\GenerativeAiOperation;
/**
 * Interface for models that support asynchronous text-to-speech conversion operations.
 *
 * Provides methods for initiating long-running text-to-speech conversion tasks.
 *
 * @since 0.1.0
 */
interface TextToSpeechConversionOperationModelInterface
{
    /**
     * Creates a text-to-speech conversion operation.
     *
     * @since 0.1.0
     *
     * @param list<Message> $prompt Array of messages containing the text to convert to speech.
     * @return GenerativeAiOperation The initiated text-to-speech conversion operation.
     */
    public function convertTextToSpeechOperation(array $prompt): GenerativeAiOperation;
}
