<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
/**
 * Represents a message in an AI conversation.
 *
 * Messages are the fundamental unit of communication with AI models,
 * containing a role and one or more parts with different content types.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type MessagePartArrayShape from MessagePart
 *
 * @phpstan-type MessageArrayShape array{
 *     role: string,
 *     parts: array<MessagePartArrayShape>
 * }
 *
 * @extends AbstractDataTransferObject<MessageArrayShape>
 */
class Message extends AbstractDataTransferObject
{
    public const KEY_ROLE = 'role';
    public const KEY_PARTS = 'parts';
    /**
     * @var MessageRoleEnum The role of the message sender.
     */
    protected MessageRoleEnum $role;
    /**
     * @var MessagePart[] The parts that make up this message.
     */
    protected array $parts;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param MessageRoleEnum $role The role of the message sender.
     * @param MessagePart[] $parts The parts that make up this message.
     * @throws InvalidArgumentException If parts contain invalid content for the role.
     */
    public function __construct(MessageRoleEnum $role, array $parts)
    {
        $this->role = $role;
        $this->parts = $parts;
        $this->validateParts();
    }
    /**
     * Gets the role of the message sender.
     *
     * @since 0.1.0
     *
     * @return MessageRoleEnum The role.
     */
    public function getRole(): MessageRoleEnum
    {
        return $this->role;
    }
    /**
     * Gets the message parts.
     *
     * @since 0.1.0
     *
     * @return MessagePart[] The message parts.
     */
    public function getParts(): array
    {
        return $this->parts;
    }
    /**
     * Returns a new instance with the given part appended.
     *
     * @since 0.1.0
     *
     * @param MessagePart $part The part to append.
     * @return Message A new instance with the part appended.
     * @throws InvalidArgumentException If the part is invalid for the role.
     */
    public function withPart(\WordPress\AiClient\Messages\DTO\MessagePart $part): \WordPress\AiClient\Messages\DTO\Message
    {
        $newParts = $this->parts;
        $newParts[] = $part;
        return new \WordPress\AiClient\Messages\DTO\Message($this->role, $newParts);
    }
    /**
     * Validates that the message parts are appropriate for the message role.
     *
     * @since 0.1.0
     *
     * @return void
     * @throws InvalidArgumentException If validation fails.
     */
    private function validateParts(): void
    {
        foreach ($this->parts as $part) {
            $type = $part->getType();
            if ($this->role->isUser() && $type->isFunctionCall()) {
                throw new InvalidArgumentException('User messages cannot contain function calls.');
            }
            if ($this->role->isModel() && $type->isFunctionResponse()) {
                throw new InvalidArgumentException('Model messages cannot contain function responses.');
            }
        }
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ROLE => ['type' => 'string', 'enum' => MessageRoleEnum::getValues(), 'description' => 'The role of the message sender.'], self::KEY_PARTS => ['type' => 'array', 'items' => \WordPress\AiClient\Messages\DTO\MessagePart::getJsonSchema(), 'minItems' => 1, 'description' => 'The parts that make up this message.']], 'required' => [self::KEY_ROLE, self::KEY_PARTS]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return MessageArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_ROLE => $this->role->value, self::KEY_PARTS => array_map(function (\WordPress\AiClient\Messages\DTO\MessagePart $part) {
            return $part->toArray();
        }, $this->parts)];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return self The specific message class based on the role.
     */
    final public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_ROLE, self::KEY_PARTS]);
        $role = MessageRoleEnum::from($array[self::KEY_ROLE]);
        $partsData = $array[self::KEY_PARTS];
        $parts = array_map(function (array $partData) {
            return \WordPress\AiClient\Messages\DTO\MessagePart::fromArray($partData);
        }, $partsData);
        // Determine which concrete class to instantiate based on role
        if ($role->isUser()) {
            return new \WordPress\AiClient\Messages\DTO\UserMessage($parts);
        } elseif ($role->isModel()) {
            return new \WordPress\AiClient\Messages\DTO\ModelMessage($parts);
        } else {
            // Only USER and MODEL roles are supported
            throw new InvalidArgumentException('Invalid message role: ' . $role->value);
        }
    }
    /**
     * Performs a deep clone of the message.
     *
     * This method ensures that message part objects are cloned to prevent
     * modifications to the cloned message from affecting the original.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        $clonedParts = [];
        foreach ($this->parts as $part) {
            $clonedParts[] = clone $part;
        }
        $this->parts = $clonedParts;
    }
}
