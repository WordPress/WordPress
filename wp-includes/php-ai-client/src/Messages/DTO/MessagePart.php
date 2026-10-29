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
 *     thoughtSignature?: string,
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
    public const KEY_THOUGHT_SIGNATURE = 'thoughtSignature';
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
     * @var string|null Thought signature for extended thinking.
     */
    private ?string $thoughtSignature = null;
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
     * @param string|null $thoughtSignature Optional thought signature for extended thinking.
     * @throws InvalidArgumentException If an unsupported content type is provided.
     */
    public function __construct($content, ?MessagePartChannelEnum $channel = null, ?string $thoughtSignature = null)
    {
        $this->channel = $channel ?? MessagePartChannelEnum::content();
        $this->thoughtSignature = $thoughtSignature;
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
     * Gets the thought signature.
     *
     * @since 1.3.0
     *
     * @return string|null The thought signature or null if not set.
     */
    public function getThoughtSignature(): ?string
    {
        return $this->thoughtSignature;
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
        $thoughtSignatureSchema = ['type' => 'string', 'description' => 'Thought signature for extended thinking.'];
        return ['oneOf' => [['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::text()->value], self::KEY_TEXT => ['type' => 'string', 'description' => 'Text content.'], self::KEY_THOUGHT_SIGNATURE => $thoughtSignatureSchema], 'required' => [self::KEY_TYPE, self::KEY_TEXT], 'additionalProperties' => \false], ['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::file()->value], self::KEY_FILE => File::getJsonSchema(), self::KEY_THOUGHT_SIGNATURE => $thoughtSignatureSchema], 'required' => [self::KEY_TYPE, self::KEY_FILE], 'additionalProperties' => \false], ['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::functionCall()->value], self::KEY_FUNCTION_CALL => FunctionCall::getJsonSchema(), self::KEY_THOUGHT_SIGNATURE => $thoughtSignatureSchema], 'required' => [self::KEY_TYPE, self::KEY_FUNCTION_CALL], 'additionalProperties' => \false], ['type' => 'object', 'properties' => [self::KEY_CHANNEL => $channelSchema, self::KEY_TYPE => ['type' => 'string', 'const' => MessagePartTypeEnum::functionResponse()->value], self::KEY_FUNCTION_RESPONSE => FunctionResponse::getJsonSchema(), self::KEY_THOUGHT_SIGNATURE => $thoughtSignatureSchema], 'required' => [self::KEY_TYPE, self::KEY_FUNCTION_RESPONSE], 'additionalProperties' => \false]]];
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
        if ($this->thoughtSignature !== null) {
            $data[self::KEY_THOUGHT_SIGNATURE] = $this->thoughtSignature;
        }
        return $data;
    }
    /**
     * {@inheritDoc}
     *
     * This check is purely structural: it does not construct the part or any nested object, so it has no
     * side effects (in particular, no filesystem access for file parts).
     *
     * @since 1.5.1
     */
    public static function isArrayShape(array $array): bool
    {
        if (isset($array[self::KEY_CHANNEL])) {
            if (!is_string($array[self::KEY_CHANNEL]) || !MessagePartChannelEnum::isValidValue($array[self::KEY_CHANNEL])) {
                return \false;
            }
        }
        if (isset($array[self::KEY_TYPE])) {
            if (!is_string($array[self::KEY_TYPE]) || !MessagePartTypeEnum::isValidValue($array[self::KEY_TYPE])) {
                return \false;
            }
        }
        if (isset($array[self::KEY_THOUGHT_SIGNATURE]) && !is_string($array[self::KEY_THOUGHT_SIGNATURE])) {
            return \false;
        }
        $contentKeys = [self::KEY_TEXT, self::KEY_FILE, self::KEY_FUNCTION_CALL, self::KEY_FUNCTION_RESPONSE];
        $presentKeys = array_values(array_filter($contentKeys, static function (string $key) use ($array): bool {
            return isset($array[$key]);
        }));
        if (count($presentKeys) !== 1) {
            return \false;
        }
        switch ($presentKeys[0]) {
            case self::KEY_TEXT:
                return is_string($array[self::KEY_TEXT]);
            case self::KEY_FILE:
                return is_array($array[self::KEY_FILE]) && File::isArrayShape($array[self::KEY_FILE]);
            case self::KEY_FUNCTION_CALL:
                return is_array($array[self::KEY_FUNCTION_CALL]) && FunctionCall::isArrayShape($array[self::KEY_FUNCTION_CALL]);
            default:
                return is_array($array[self::KEY_FUNCTION_RESPONSE]) && FunctionResponse::isArrayShape($array[self::KEY_FUNCTION_RESPONSE]);
        }
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     * @since 1.5.1 Values of the wrong type are rejected with an InvalidArgumentException.
     */
    public static function fromArray(array $array): self
    {
        if (isset($array[self::KEY_CHANNEL])) {
            if (!is_string($array[self::KEY_CHANNEL])) {
                throw new InvalidArgumentException('MessagePart channel must be a string.');
            }
            $channel = MessagePartChannelEnum::from($array[self::KEY_CHANNEL]);
        } else {
            $channel = null;
        }
        $thoughtSignature = $array[self::KEY_THOUGHT_SIGNATURE] ?? null;
        if ($thoughtSignature !== null && !is_string($thoughtSignature)) {
            throw new InvalidArgumentException('MessagePart thoughtSignature must be a string.');
        }
        // Check which properties are set to determine how to construct the MessagePart
        if (isset($array[self::KEY_TEXT])) {
            if (!is_string($array[self::KEY_TEXT])) {
                throw new InvalidArgumentException('MessagePart text must be a string.');
            }
            return new self($array[self::KEY_TEXT], $channel, $thoughtSignature);
        } elseif (isset($array[self::KEY_FILE])) {
            if (!is_array($array[self::KEY_FILE])) {
                throw new InvalidArgumentException('MessagePart file must be an array.');
            }
            return new self(File::fromArray($array[self::KEY_FILE]), $channel, $thoughtSignature);
        } elseif (isset($array[self::KEY_FUNCTION_CALL])) {
            if (!is_array($array[self::KEY_FUNCTION_CALL])) {
                throw new InvalidArgumentException('MessagePart functionCall must be an array.');
            }
            return new self(FunctionCall::fromArray($array[self::KEY_FUNCTION_CALL]), $channel, $thoughtSignature);
        } elseif (isset($array[self::KEY_FUNCTION_RESPONSE])) {
            if (!is_array($array[self::KEY_FUNCTION_RESPONSE])) {
                throw new InvalidArgumentException('MessagePart functionResponse must be an array.');
            }
            return new self(FunctionResponse::fromArray($array[self::KEY_FUNCTION_RESPONSE]), $channel, $thoughtSignature);
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
