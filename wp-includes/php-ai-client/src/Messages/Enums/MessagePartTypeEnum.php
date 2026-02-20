<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for message part types.
 *
 * @since 0.1.0
 *
 * @method static self text() Creates an instance for TEXT type.
 * @method static self file() Creates an instance for FILE type.
 * @method static self functionCall() Creates an instance for FUNCTION_CALL type.
 * @method static self functionResponse() Creates an instance for FUNCTION_RESPONSE type.
 * @method bool isText() Checks if the type is TEXT.
 * @method bool isFile() Checks if the type is FILE.
 * @method bool isFunctionCall() Checks if the type is FUNCTION_CALL.
 * @method bool isFunctionResponse() Checks if the type is FUNCTION_RESPONSE.
 */
class MessagePartTypeEnum extends AbstractEnum
{
    /**
     * Text content.
     */
    public const TEXT = 'text';
    /**
     * File content (inline or remote).
     */
    public const FILE = 'file';
    /**
     * Function call request.
     */
    public const FUNCTION_CALL = 'function_call';
    /**
     * Function response.
     */
    public const FUNCTION_RESPONSE = 'function_response';
}
