<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for message roles in AI conversations.
 *
 * @since 0.1.0
 *
 * @method static self user() Creates an instance for USER role.
 * @method static self model() Creates an instance for MODEL role.
 * @method bool isUser() Checks if the role is USER.
 * @method bool isModel() Checks if the role is MODEL.
 */
class MessageRoleEnum extends AbstractEnum
{
    /**
     * User role - messages from the user.
     */
    public const USER = 'user';
    /**
     * Model role - messages from the AI model.
     */
    public const MODEL = 'model';
}
