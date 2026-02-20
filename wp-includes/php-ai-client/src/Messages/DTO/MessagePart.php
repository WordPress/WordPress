<?php

declare (strict_types=1);
namespace WordPress\AiClient\Messages\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Messages\Enums\MessagePartChannelEnum;
use WordPress\AiClient\Messages\Enums\MessagePartTypeEnum;
use WordPress\AiClient\Tools\DTO\FunctionCall;
use WordPress\AiClient\Tools\DTO\FunctionResponse;
/**
 * Represents a part of a message.
 *
 * Messages can contain multiple parts of different types, such as text, files,
 * function calls, etc. This DTO encapsulates one such part.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type FileArrayShape from File
 * @phpstan-import-type FunctionCallArrayShape from FunctionCall
 * @phpstan-import-type FunctionResponseArrayShape from FunctionResponse
 *
 * @phpstan-type MessagePartArrayShape array{
 *     channel: string,
 *     type: string,
 *     text?: string,
 *     file?: FileArrayShape,
 *     functionCall?: FunctionCallArrayShape,
 *     functionResponse?: FunctionResponseArrayShape
 * }
 *
 * @extends AbstractDataTransferObject<MessagePartArrayShape>
 */
class MessagePart extends AbstractDataTransferObject
{
    public const KEY_CHANNEL = 'channel';
    public const KEY_TYPE = 'type';
    public const KEY_TEXT = 'text';
    public const KEY_FILE = 'file';
    public const KEY_FUNCTION_CALL = 'functionCall';
    public const KEY_FUNCTION_RESPONSE = 'functionResponse';
    /**
     * @var MessagePartChannelEnum The channel this message part belongs to.
     */
    private MessagePartChannelEnum $channel;
    /**
     * @var MessagePartTypeEnum The type of this message part.
     */
    private MessagePartTypeEnum $type;
    /**
     * @var string|null Text content (when type is TEXT).
     */
    private ?string $text = null;
    /**
     * @var File|null File data (when type is FILE).
     */
    private ?File $file = null;
    /**
     * @var FunctionCall|null Function call request (when type is FUNCTION_CALL).
     */
    private ?FunctionCall $functionCall = null;
    /**
     * @var FunctionResponse|null Function response (when type is FUNCTION_RESPONSE).
     */
    private ?FunctionResponse $functionResponse = null;
    /**
     * Constructor that accepts various content types and infers the message part type.
     *
     * @since 0.1.0
     *
     * @param mixed $content The content of this message part.
     * @param MessagePartChannelEnum|null $channel The channel this part belongs to. Defaults to CONTENT.
     * @throws InvalidArgumentException If an unsupported content type is provided.
     */
    public function __construct($content, ?MessagePartChannelEnum $channel = null)
    {
        $this->channel = $channel ?? MessagePartChannelEnum::content();
        if (is_string($content)) {
            $this->type = MessagePartTypeEnum::text();
            $this->text = $content;
        } elseif ($content instanceof File) {
            $this->type = MessagePartTypeEnum::file();
            $this->file = $content;
        } elseif ($content instanceof FunctionCall) {
            $this->type = MessagePartTypeEnum::functionCall();
            $this->functionCall = $content;
        } elseif ($content instanceof FunctionResponse) {
            $this->type = MessagePartTypeEnum::functionResponse();
            $this->functionResponse = $content;
        } else {
            $type = is_object($content) ? get_class($content) : gettype($content);
            throw new InvalidArgumentException(sprintf('Unsupported content type %s. Expected string, File, ' . 'FunctionCall, or FunctionResponse.', $type));
        }
    }
    /**
     * Gets the channel this message part belongs to.
     *
     * @since 0.1.0
     *
     * @return MessagePartChannelEnum The channel.
     */
    public function getChannel(): MessagePartChannelEnum
    {
        return $this->channel;
    }
    /**
     * Gets the type of this message part.
     *
     * @since 0.1.0
     *
     * @return MessagePartTypeEnum The type.
     */
    public function getType(): MessagePartTypeEnum
    {
        return $this->type;
    }
    /**
     * Gets the text content.
     *
     * @since 0.1.0
     *
     * @return string|null The text content or null if not a text part.
     */
    public function getText(): ?string
    {
        return $this->text;
    }
    /**
     * Gets the file.
     *
     * @since 0.1.0
     *
     * @return File|null The file or null if not a file part.
     */
    public function getFile(): ?File
    {
        return $this->file;
    }
    /**
     * Gets the function call.
     *
     * @since 0.1.0
     *
     * @return FunctionCall|null The function call or null if not a function call part.
     */
    public function getFunctionCall(): ?FunctionCall
    {
        return $this->functionCall;
    }
    /**
     * Gets the function response.
     *
     * @since 0.1.0
     *
     * @return FunctionResponse|null The function response or null if not a function response part.
     */
    public function getFunctionResponse(): ?FunctionResponse
    {
        return $this->functionResponse;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        $channelSchema = ['type' => 'string', 'enum' => MessagePartChannelEnum::getValues(), 'description' => 'The channel this message part belongs to.'];
        return ['oneOf' => [['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::text()->value], self::KEY_TEXT => ['type' => 'string', 'description' => 'Text content.']], 'required' => [self::KEY_TYPE, self::KEY_TEXT], 'additionalProperties' => \false], ['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::file()->value], self::KEY_FILE => File::getJsonSchema()], 'required' => [self::KEY_TYPE, self::KEY_FILE], 'additionalProperties' => \false], ['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::functionCall()->value], self::KEY_FUNCTION_CALL => FunctionCall::getJsonSchema()], 'required' => [self::KEY_TYPE, self::KEY_FUNCTION_CALL], 'additionalProperties' => \false], ['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::functionResponse()->value], self::KEY_FUNCTION_RESPONSE => FunctionResponse::getJsonSchema()], 'required' => [self::KEY_TYPE, self::KEY_FUNCTION_RESPONSE], 'additionalProperties' => \false]]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return MessagePartArrayShape
     */
    public function toArray(): array
    {
        $data = [self::KEY_CHANNEL => $this->channel->value, self::KEY_TYPE => $this->type->value];
        if ($this->text !== null) {
            $data[self::KEY_TEXT] = $this->text;
        } elseif ($this->file !== null) {
            $data[self::KEY_FILE] = $this->file->toArray();
        } elseif ($this->functionCall !== null) {
            $data[self::KEY_FUNCTION_CALL] = $this->functionCall->toArray();
        } elseif ($this->functionResponse !== null) {
            $data[self::KEY_FUNCTION_RESPONSE] = $this->functionResponse->toArray();
        } else {
            throw new RuntimeException('MessagePart requires one of: text, file, functionCall, or functionResponse. ' . 'This should not be a possible condition.');
        }
        return $data;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        if (isset($array[self::KEY_CHANNEL])) {
            $channel = MessagePartChannelEnum::from($array[self::KEY_CHANNEL]);
        } else {
            $channel = null;
        }
        // Check which properties are set to determine how to construct the MessagePart
        if (isset($array[self::KEY_TEXT])) {
            return new self($array[self::KEY_TEXT], $channel);
        } elseif (isset($array[self::KEY_FILE])) {
            return new self(File::fromArray($array[self::KEY_FILE]), $channel);
        } elseif (isset($array[self::KEY_FUNCTION_CALL])) {
            return new self(FunctionCall::fromArray($array[self::KEY_FUNCTION_CALL]), $channel);
        } elseif (isset($array[self::KEY_FUNCTION_RESPONSE])) {
            return new self(FunctionResponse::fromArray($array[self::KEY_FUNCTION_RESPONSE]), $channel);
        } else {
            throw new InvalidArgumentException('MessagePart requires one of: text, file, functionCall, or functionResponse.');
        }
    }
    /**
     * Performs a deep clone of the message part.
     *
     * This method ensures that nested objects (file, function call, function response)
     * are cloned to prevent modifications to the cloned part from affecting the original.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        if ($this->file !== null) {
            $this->file = clone $this->file;
        }
        if ($this->functionCall !== null) {
            $this->functionCall = clone $this->functionCall;
        }
        if ($this->functionResponse !== null) {
            $this->functionResponse = clone $this->functionResponse;
        }
    }
}
