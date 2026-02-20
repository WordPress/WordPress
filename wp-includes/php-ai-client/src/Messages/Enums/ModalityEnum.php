<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for input/output modalities.
 *
 * @since 0.1.0
 *
 * @method static self text() Creates an instance for TEXT modality.
 * @method static self document() Creates an instance for DOCUMENT modality.
 * @method static self image() Creates an instance for IMAGE modality.
 * @method static self audio() Creates an instance for AUDIO modality.
 * @method static self video() Creates an instance for VIDEO modality.
 * @method bool isText() Checks if the modality is TEXT.
 * @method bool isDocument() Checks if the modality is DOCUMENT.
 * @method bool isImage() Checks if the modality is IMAGE.
 * @method bool isAudio() Checks if the modality is AUDIO.
 * @method bool isVideo() Checks if the modality is VIDEO.
 */
class ModalityEnum extends AbstractEnum
{
    /**
     * Text modality.
     */
    public const TEXT = 'text';
    /**
     * Document modality (PDFs, Word docs, etc.).
     */
    public const DOCUMENT = 'document';
    /**
     * Image modality.
     */
    public const IMAGE = 'image';
    /**
     * Audio modality.
     */
    public const AUDIO = 'audio';
    /**
     * Video modality.
     */
    public const VIDEO = 'video';
}
