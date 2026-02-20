<?php

declare (strict_types=1);
namespace WordPress\AiClient\Builders;

use InvalidArgumentException;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Messages\Enums\MessageRoleEnum;
use WordPress\AiClient\Tools\DTO\FunctionCall;
use WordPress\AiClient\Tools\DTO\FunctionResponse;
/**
 * Fluent builder for constructing AI messages.
 *
 * This class provides a fluent interface for building messages with various
 * content types including text, files, function calls, and function responses.
 *
 * @since 0.2.0
 *
 * @phpstan-import-type MessagePartArrayShape from MessagePart
 *
 * @phpstan-type Input string|MessagePart|MessagePartArrayShape|File|FunctionCall|FunctionResponse|null
 */
class MessageBuilder
{
    /**
     * @var MessageRoleEnum|null The role of the message sender.
     */
    protected ?MessageRoleEnum $role = null;
    /**
     * @var list<MessagePart> The parts that make up the message.
     */
    protected array $parts = [];
    /**
     * Constructor.
     *
     * @since 0.2.0
     *
     * @param Input $input Optional initial content.
     * @param MessageRoleEnum|null $role Optional role.
     */
    public function __construct($input = null, ?MessageRoleEnum $role = null)
    {
        $this->role = $role;
        if ($input === null) {
            return;
        }
        // Handle different input types
        if ($input instanceof MessagePart) {
            $this->parts[] = $input;
        } elseif (is_string($input)) {
            $this->withText($input);
        } elseif ($input instanceof File) {
            $this->withFile($input);
        } elseif ($input instanceof FunctionCall) {
            $this->withFunctionCall($input);
        } elseif ($input instanceof FunctionResponse) {
            $this->withFunctionResponse($input);
        } elseif (is_array($input) && MessagePart::isArrayShape($input)) {
            $this->parts[] = MessagePart::fromArray($input);
        } else {
            throw new InvalidArgumentException('Input must be a string, MessagePart, MessagePartArrayShape, File, FunctionCall, or FunctionResponse.');
        }
    }
    /**
     * Creates a deep clone of this builder.
     *
     * Clones all MessagePart objects in the parts array to ensure
     * the cloned builder is independent of the original.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        // Deep clone parts array (MessagePart has __clone)
        $clonedParts = [];
        foreach ($this->parts as $part) {
            $clonedParts[] = clone $part;
        }
        $this->parts = $clonedParts;
        // Note: $role is an enum value object and can be safely shared
    }
    /**
     * Sets the role of the message sender.
     *
     * @since 0.2.0
     *
     * @param MessageRoleEnum $role The role to set.
     * @return self
     */
    public function usingRole(MessageRoleEnum $role): self
    {
        $this->role = $role;
        return $this;
    }
    /**
     * Sets the role to user.
     *
     * @since 0.2.0
     *
     * @return self
     */
    public function usingUserRole(): self
    {
        return $this->usingRole(MessageRoleEnum::user());
    }
    /**
     * Sets the role to model.
     *
     * @since 0.2.0
     *
     * @return self
     */
    public function usingModelRole(): self
    {
        return $this->usingRole(MessageRoleEnum::model());
    }
    /**
     * Adds text content to the message.
     *
     * @since 0.2.0
     *
     * @param string $text The text to add.
     * @return self
     * @throws InvalidArgumentException If the text is empty.
     */
    public function withText(string $text): self
    {
        if (trim($text) === '') {
            throw new InvalidArgumentException('Text content cannot be empty.');
        }
        $this->parts[] = new MessagePart($text);
        return $this;
    }
    /**
     * Adds a file to the message.
     *
     * Accepts:
     * - File object
     * - URL string (remote file)
     * - Base64-encoded data string
     * - Data URI string (data:mime/type;base64,data)
     * - Local file path string
     *
     * @since 0.2.0
     *
     * @param string|File $file The file to add.
     * @param string|null $mimeType Optional MIME type (ignored if File object provided).
     * @return self
     * @throws InvalidArgumentException If the file is invalid.
     */
    public function withFile($file, ?string $mimeType = null): self
    {
        $file = $file instanceof File ? $file : new File($file, $mimeType);
        $this->parts[] = new MessagePart($file);
        return $this;
    }
    /**
     * Adds a function call to the message.
     *
     * @since 0.2.0
     *
     * @param FunctionCall $functionCall The function call to add.
     * @return self
     */
    public function withFunctionCall(FunctionCall $functionCall): self
    {
        $this->parts[] = new MessagePart($functionCall);
        return $this;
    }
    /**
     * Adds a function response to the message.
     *
     * @since 0.2.0
     *
     * @param FunctionResponse $functionResponse The function response to add.
     * @return self
     */
    public function withFunctionResponse(FunctionResponse $functionResponse): self
    {
        $this->parts[] = new MessagePart($functionResponse);
        return $this;
    }
    /**
     * Adds multiple message parts to the message.
     *
     * @since 0.2.0
     *
     * @param MessagePart ...$parts The message parts to add.
     * @return self
     */
    public function withMessageParts(MessagePart ...$parts): self
    {
        foreach ($parts as $part) {
            $this->parts[] = $part;
        }
        return $this;
    }
    /**
     * Builds and returns the Message object.
     *
     * @since 0.2.0
     *
     * @return Message The built message.
     * @throws InvalidArgumentException If the message validation fails.
     */
    public function get(): Message
    {
        if (empty($this->parts)) {
            throw new InvalidArgumentException('Cannot build an empty message. Add content using withText() or similar methods.');
        }
        if ($this->role === null) {
            throw new InvalidArgumentException('Cannot build a message with no role. Set a role using usingRole() or similar methods.');
        }
        // At this point, we've validated that $this->role is not null
        /** @var MessageRoleEnum $role */
        $role = $this->role;
        return new Message($role, $this->parts);
    }
}
