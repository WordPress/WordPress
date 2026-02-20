<?php

declare (strict_types=1);
namespace WordPress\AiClient\Events;

use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
/**
 * Event dispatched after a prompt has been sent to the AI model and a response received.
 *
 * This event allows listeners to inspect the result of the model call for logging,
 * analytics, or other post-processing purposes. The result object is immutable.
 *
 * @since 0.4.0
 */
class AfterGenerateResultEvent
{
    /**
     * @var list<Message> The messages that were sent to the model.
     */
    private array $messages;
    /**
     * @var ModelInterface The model that processed the prompt.
     */
    private ModelInterface $model;
    /**
     * @var CapabilityEnum|null The capability that was used for generation.
     */
    private ?CapabilityEnum $capability;
    /**
     * @var GenerativeAiResult The result from the model.
     */
    private GenerativeAiResult $result;
    /**
     * Constructor.
     *
     * @since 0.4.0
     *
     * @param list<Message> $messages The messages that were sent to the model.
     * @param ModelInterface $model The model that processed the prompt.
     * @param CapabilityEnum|null $capability The capability that was used for generation.
     * @param GenerativeAiResult $result The result from the model.
     */
    public function __construct(array $messages, ModelInterface $model, ?CapabilityEnum $capability, GenerativeAiResult $result)
    {
        $this->messages = $messages;
        $this->model = $model;
        $this->capability = $capability;
        $this->result = $result;
    }
    /**
     * Gets the messages that were sent to the model.
     *
     * @since 0.4.0
     *
     * @return list<Message> The messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
    /**
     * Gets the model that processed the prompt.
     *
     * @since 0.4.0
     *
     * @return ModelInterface The model.
     */
    public function getModel(): ModelInterface
    {
        return $this->model;
    }
    /**
     * Gets the capability that was used for generation.
     *
     * @since 0.4.0
     *
     * @return CapabilityEnum|null The capability, or null if not specified.
     */
    public function getCapability(): ?CapabilityEnum
    {
        return $this->capability;
    }
    /**
     * Gets the result from the model.
     *
     * @since 0.4.0
     *
     * @return GenerativeAiResult The result.
     */
    public function getResult(): GenerativeAiResult
    {
        return $this->result;
    }
    /**
     * Performs a deep clone of the event.
     *
     * This method ensures that message and result objects are cloned to prevent
     * modifications to the cloned event from affecting the original.
     * The model object is not cloned as it is a service object.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        $clonedMessages = [];
        foreach ($this->messages as $message) {
            $clonedMessages[] = clone $message;
        }
        $this->messages = $clonedMessages;
        $this->result = clone $this->result;
    }
}
