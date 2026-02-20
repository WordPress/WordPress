<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for model capabilities.
 *
 * @since 0.1.0
 *
 * @method static self textGeneration() Creates an instance for TEXT_GENERATION capability.
 * @method static self imageGeneration() Creates an instance for IMAGE_GENERATION capability.
 * @method static self textToSpeechConversion() Creates an instance for TEXT_TO_SPEECH_CONVERSION capability.
 * @method static self speechGeneration() Creates an instance for SPEECH_GENERATION capability.
 * @method static self musicGeneration() Creates an instance for MUSIC_GENERATION capability.
 * @method static self videoGeneration() Creates an instance for VIDEO_GENERATION capability.
 * @method static self embeddingGeneration() Creates an instance for EMBEDDING_GENERATION capability.
 * @method static self chatHistory() Creates an instance for CHAT_HISTORY capability.
 * @method bool isTextGeneration() Checks if the capability is TEXT_GENERATION.
 * @method bool isImageGeneration() Checks if the capability is IMAGE_GENERATION.
 * @method bool isTextToSpeechConversion() Checks if the capability is TEXT_TO_SPEECH_CONVERSION.
 * @method bool isSpeechGeneration() Checks if the capability is SPEECH_GENERATION.
 * @method bool isMusicGeneration() Checks if the capability is MUSIC_GENERATION.
 * @method bool isVideoGeneration() Checks if the capability is VIDEO_GENERATION.
 * @method bool isEmbeddingGeneration() Checks if the capability is EMBEDDING_GENERATION.
 * @method bool isChatHistory() Checks if the capability is CHAT_HISTORY.
 */
class CapabilityEnum extends AbstractEnum
{
    /**
     * Text generation capability.
     */
    public const TEXT_GENERATION = 'text_generation';
    /**
     * Image generation capability.
     */
    public const IMAGE_GENERATION = 'image_generation';
    /**
     * Text to speech conversion capability.
     */
    public const TEXT_TO_SPEECH_CONVERSION = 'text_to_speech_conversion';
    /**
     * Speech generation capability.
     */
    public const SPEECH_GENERATION = 'speech_generation';
    /**
     * Music generation capability.
     */
    public const MUSIC_GENERATION = 'music_generation';
    /**
     * Video generation capability.
     */
    public const VIDEO_GENERATION = 'video_generation';
    /**
     * Embedding generation capability.
     */
    public const EMBEDDING_GENERATION = 'embedding_generation';
    /**
     * Chat history support capability.
     */
    public const CHAT_HISTORY = 'chat_history';
}
