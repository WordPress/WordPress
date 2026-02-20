<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\SpeechGeneration\Contracts;

use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
/**
 * Interface for models that support speech generation.
 *
 * Provides synchronous methods for generating speech from prompts.
 *
 * @since 0.1.0
 */
interface SpeechGenerationModelInterface
{
    /**
     * Generates speech from a prompt.
     *
     * @since 0.1.0
     *
     * @param list<Message> $prompt Array of messages containing the speech generation prompt.
     * @return GenerativeAiResult Result containing generated speech audio.
     */
    public function generateSpeechResult(array $prompt): GenerativeAiResult;
}
