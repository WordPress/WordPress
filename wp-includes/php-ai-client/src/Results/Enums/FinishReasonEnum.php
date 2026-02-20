<?php

declare (strict_types=1);
namespace WordPress\AiClient\Results\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for finish reasons of AI generation.
 *
 * @since 0.1.0
 *
 * @method static self stop() Creates an instance for STOP reason.
 * @method static self length() Creates an instance for LENGTH reason.
 * @method static self contentFilter() Creates an instance for CONTENT_FILTER reason.
 * @method static self toolCalls() Creates an instance for TOOL_CALLS reason.
 * @method static self error() Creates an instance for ERROR reason.
 * @method bool isStop() Checks if the reason is STOP.
 * @method bool isLength() Checks if the reason is LENGTH.
 * @method bool isContentFilter() Checks if the reason is CONTENT_FILTER.
 * @method bool isToolCalls() Checks if the reason is TOOL_CALLS.
 * @method bool isError() Checks if the reason is ERROR.
 */
class FinishReasonEnum extends AbstractEnum
{
    /**
     * Generation stopped naturally.
     */
    public const STOP = 'stop';
    /**
     * Generation stopped due to max length.
     */
    public const LENGTH = 'length';
    /**
     * Generation stopped due to content filter.
     */
    public const CONTENT_FILTER = 'content_filter';
    /**
     * Generation stopped to make tool calls.
     */
    public const TOOL_CALLS = 'tool_calls';
    /**
     * Generation stopped due to error.
     */
    public const ERROR = 'error';
}
