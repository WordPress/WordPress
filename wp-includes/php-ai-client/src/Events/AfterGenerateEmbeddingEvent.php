<?php

declare (strict_types=1);
namespace WordPress\AiClient\Events;

use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Results\DTO\EmbeddingResult;
/**
 * Event dispatched after embeddings have been generated.
 *
 * @since 1.4.0
 */
class AfterGenerateEmbeddingEvent
{
    /**
     * @var list<MessagePart> The inputs that were sent to the model.
     */
    private array $inputs;
    /**
     * @var ModelInterface The model that generated embeddings.
     */
    private ModelInterface $model;
    /**
     * @var CapabilityEnum The capability that was used for generation.
     */
    private CapabilityEnum $capability;
    /**
     * @var EmbeddingResult The result from the model.
     */
    private EmbeddingResult $result;
    /**
     * Constructor.
     *
     * @since 1.4.0
     *
     * @param list<MessagePart> $inputs The inputs that were sent to the model.
     * @param ModelInterface    $model The model that generated embeddings.
     * @param CapabilityEnum    $capability The capability that was used for generation.
     * @param EmbeddingResult   $result The result from the model.
     */
    public function __construct(array $inputs, ModelInterface $model, CapabilityEnum $capability, EmbeddingResult $result)
    {
        $this->inputs = $inputs;
        $this->model = $model;
        $this->capability = $capability;
        $this->result = $result;
    }
    /**
     * Gets the inputs that were sent to the model.
     *
     * @since 1.4.0
     *
     * @return list<MessagePart> The inputs.
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }
    /**
     * Gets the model that generated embeddings.
     *
     * @since 1.4.0
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
     * @since 1.4.0
     *
     * @return CapabilityEnum The capability.
     */
    public function getCapability(): CapabilityEnum
    {
        return $this->capability;
    }
    /**
     * Gets the result from the model.
     *
     * @since 1.4.0
     *
     * @return EmbeddingResult The result.
     */
    public function getResult(): EmbeddingResult
    {
        return $this->result;
    }
    /**
     * Performs a deep clone of the event.
     *
     * @since 1.4.0
     */
    public function __clone()
    {
        $clonedInputs = [];
        foreach ($this->inputs as $input) {
            $clonedInputs[] = clone $input;
        }
        $this->inputs = $clonedInputs;
        $this->result = clone $this->result;
    }
}
