<?php

declare (strict_types=1);
namespace WordPress\AiClient\Operations\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for operation states.
 *
 * @since 0.1.0
 *
 * @method static self starting() Creates an instance for STARTING state.
 * @method static self processing() Creates an instance for PROCESSING state.
 * @method static self succeeded() Creates an instance for SUCCEEDED state.
 * @method static self failed() Creates an instance for FAILED state.
 * @method static self canceled() Creates an instance for CANCELED state.
 * @method bool isStarting() Checks if the state is STARTING.
 * @method bool isProcessing() Checks if the state is PROCESSING.
 * @method bool isSucceeded() Checks if the state is SUCCEEDED.
 * @method bool isFailed() Checks if the state is FAILED.
 * @method bool isCanceled() Checks if the state is CANCELED.
 */
class OperationStateEnum extends AbstractEnum
{
    /**
     * Operation is starting.
     */
    public const STARTING = 'starting';
    /**
     * Operation is processing.
     */
    public const PROCESSING = 'processing';
    /**
     * Operation succeeded.
     */
    public const SUCCEEDED = 'succeeded';
    /**
     * Operation failed.
     */
    public const FAILED = 'failed';
    /**
     * Operation was canceled.
     */
    public const CANCELED = 'canceled';
}
