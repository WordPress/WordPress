<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for tool types.
 *
 * @since 0.1.0
 *
 * @method static self functionDeclarations() Creates an instance for FUNCTION_DECLARATIONS type.
 * @method static self webSearch() Creates an instance for WEB_SEARCH type.
 * @method bool isFunctionDeclarations() Checks if the type is FUNCTION_DECLARATIONS.
 * @method bool isWebSearch() Checks if the type is WEB_SEARCH.
 */
class ToolTypeEnum extends AbstractEnum
{
    /**
     * Function declarations tool type.
     */
    public const FUNCTION_DECLARATIONS = 'function_declarations';
    /**
     * Web search tool type.
     */
    public const WEB_SEARCH = 'web_search';
}
