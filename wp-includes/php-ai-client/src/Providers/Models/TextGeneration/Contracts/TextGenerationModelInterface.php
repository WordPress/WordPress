<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\TextGeneration\Contracts;

use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
/**
 * Interface for models that support text generation.
 *
 * Provides synchronous and streaming methods for generating text from prompts.
 *
 * @since 0.1.0
 */
interface TextGenerationModelInterface
{
    /**
     * Generates text from a prompt.
     *
     * @since 0.1.0
     *
     * @param list<Message> $prompt Array of messages containing the text generation prompt.
     * @return GenerativeAiResult Result containing generated text.
     */
    public function generateTextResult(array $prompt): GenerativeAiResult;
}
