<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\DTO;

use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
/**
 * Represents a message from the AI model.
 *
 * This is a convenience class that automatically sets the role to MODEL.
 * Model messages contain the AI's responses.
 *
 * Important: Do not rely on `instanceof ModelMessage` to determine the message role.
 * This is merely a helper class for construction. Always use `$message->getRole()`
 * to check the role of a message.
 *
 * @since 0.1.0
 */
class ModelMessage extends \WordPress\AiClient\Messages\DTO\Message
{
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param MessagePart[] $parts The parts that make up this message.
     */
    public function __construct(array $parts)
    {
        parent::__construct(MessageRoleEnum::model(), $parts);
    }
}
