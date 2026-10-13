<?php

declare (strict_types=1);
namespace WordPress\AiClient\Events;

use WordPress\AiClient\Messages\DTO\MessagePart;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
/**
 * Event dispatched before inputs are sent to an embedding generation model.
 *
 * @since 1.4.0
 */
class BeforeGenerateEmbeddingEvent
{
    /**
     * @var list<MessagePart> The inputs to be sent to the model.
     */
    private array $inputs;
    /**
     * @var ModelInterface The model that will generate embeddings.
     */
    private ModelInterface $model;
    /**
     * @var CapabilityEnum The capability being used for generation.
     */
    private CapabilityEnum $capability;
    /**
     * Constructor.
     *
     * @since 1.4.0
     *
     * @param list<MessagePart> $inputs The inputs to be sent to the model.
     * @param ModelInterface    $model The model that will generate embeddings.
     * @param CapabilityEnum    $capability The capability being used for generation.
     */
    public function __construct(array $inputs, ModelInterface $model, CapabilityEnum $capability)
    {
        $this->inputs = $inputs;
        $this->model = $model;
        $this->capability = $capability;
    }
    /**
     * Gets the inputs to be sent to the model.
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
     * Gets the model that will generate embeddings.
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
     * Gets the capability being used for generation.
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
    }
}
