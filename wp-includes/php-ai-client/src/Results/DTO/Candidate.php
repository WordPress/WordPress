<?php

declare (strict_types=1);
namespace WordPress\AiClient\Results\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Results\Enums\FinishReasonEnum;
/**
 * Represents a candidate response from an AI model.
 *
 * When generating content, AI models can produce multiple candidates.
 * Each candidate contains a message and metadata about why generation stopped.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type MessageArrayShape from Message
 *
 * @phpstan-type CandidateArrayShape array{message: MessageArrayShape, finishReason: string}
 *
 * @extends AbstractDataTransferObject<CandidateArrayShape>
 */
class Candidate extends AbstractDataTransferObject
{
    public const KEY_MESSAGE = 'message';
    public const KEY_FINISH_REASON = 'finishReason';
    /**
     * @var Message The generated message.
     */
    private Message $message;
    /**
     * @var FinishReasonEnum The reason generation stopped.
     */
    private FinishReasonEnum $finishReason;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param Message $message The generated message.
     * @param FinishReasonEnum $finishReason The reason generation stopped.
     */
    public function __construct(Message $message, FinishReasonEnum $finishReason)
    {
        if (!$message->getRole()->isModel()) {
            throw new InvalidArgumentException('Message must be a model message.');
        }
        $this->message = $message;
        $this->finishReason = $finishReason;
    }
    /**
     * Gets the generated message.
     *
     * @since 0.1.0
     *
     * @return Message The message.
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
    /**
     * Gets the finish reason.
     *
     * @since 0.1.0
     *
     * @return FinishReasonEnum The finish reason.
     */
    public function getFinishReason(): FinishReasonEnum
    {
        return $this->finishReason;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_MESSAGE => Message::getJsonSchema(), self::KEY_FINISH_REASON => ['type' => 'string', 'enum' => FinishReasonEnum::getValues(), 'description' => 'The reason generation stopped.']], 'required' => [self::KEY_MESSAGE, self::KEY_FINISH_REASON]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return CandidateArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_MESSAGE => $this->message->toArray(), self::KEY_FINISH_REASON => $this->finishReason->value];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_MESSAGE, self::KEY_FINISH_REASON]);
        $messageData = $array[self::KEY_MESSAGE];
        return new self(Message::fromArray($messageData), FinishReasonEnum::from($array[self::KEY_FINISH_REASON]));
    }
    /**
     * Performs a deep clone of the candidate.
     *
     * This method ensures that the message object is cloned to prevent
     * modifications to the cloned candidate from affecting the original.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        $this->message = clone $this->message;
    }
}
