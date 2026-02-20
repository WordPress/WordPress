<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\ImageGeneration\Contracts;

use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
/**
 * Interface for models that support image generation.
 *
 * Provides synchronous methods for generating images from text prompts.
 *
 * @since 0.1.0
 */
interface ImageGenerationModelInterface
{
    /**
     * Generates images from a prompt.
     *
     * @since 0.1.0
     *
     * @param list<Message> $prompt Array of messages containing the image generation prompt.
     * @return GenerativeAiResult Result containing generated images.
     */
    public function generateImageResult(array $prompt): GenerativeAiResult;
}
