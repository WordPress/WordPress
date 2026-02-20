<?php

declare (strict_types=1);
namespace WordPress\AiClient\Operations\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Operations\Contracts\OperationInterface;
use WordPress\AiClient\Operations\Enums\OperationStateEnum;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;
/**
 * Represents a long-running generative AI operation.
 *
 * This DTO tracks the progress of generative AI tasks that may not complete
 * immediately, providing access to the result once available.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type GenerativeAiResultArrayShape from GenerativeAiResult
 *
 * @phpstan-type GenerativeAiOperationArrayShape array{id: string, state: string, result?: GenerativeAiResultArrayShape}
 *
 * @extends AbstractDataTransferObject<GenerativeAiOperationArrayShape>
 */
class GenerativeAiOperation extends AbstractDataTransferObject implements OperationInterface
{
    public const KEY_ID = 'id';
    public const KEY_STATE = 'state';
    public const KEY_RESULT = 'result';
    /**
     * @var string Unique identifier for this operation.
     */
    private string $id;
    /**
     * @var OperationStateEnum The current state of the operation.
     */
    private OperationStateEnum $state;
    /**
     * @var GenerativeAiResult|null The result once the operation completes.
     */
    private ?GenerativeAiResult $result;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $id Unique identifier for this operation.
     * @param OperationStateEnum $state The current state of the operation.
     * @param GenerativeAiResult|null $result The result once the operation completes.
     */
    public function __construct(string $id, OperationStateEnum $state, ?GenerativeAiResult $result = null)
    {
        $this->id = $id;
        $this->state = $state;
        $this->result = $result;
    }
    /**
     * Creates a deep clone of this operation.
     *
     * Clones the result object if present to ensure the cloned
     * operation is independent of the original.
     * The state enum is immutable and can be safely shared.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        // Clone the result if present (GenerativeAiResult has __clone)
        if ($this->result !== null) {
            $this->result = clone $this->result;
        }
        // Note: $state is an immutable enum and can be safely shared
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function getId(): string
    {
        return $this->id;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function getState(): OperationStateEnum
    {
        return $this->state;
    }
    /**
     * Gets the operation result.
     *
     * @since 0.1.0
     *
     * @return GenerativeAiResult|null The result or null if not yet complete.
     */
    public function getResult(): ?GenerativeAiResult
    {
        return $this->result;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['oneOf' => [
            // Succeeded state - has result
            ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'Unique identifier for this operation.'], self::KEY_STATE => ['type' => 'string', 'const' => OperationStateEnum::succeeded()->value], self::KEY_RESULT => GenerativeAiResult::getJsonSchema()], 'required' => [self::KEY_ID, self::KEY_STATE, self::KEY_RESULT], 'additionalProperties' => \false],
            // All other states - no result
            ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'Unique identifier for this operation.'], self::KEY_STATE => ['type' => 'string', 'enum' => [OperationStateEnum::starting()->value, OperationStateEnum::processing()->value, OperationStateEnum::failed()->value, OperationStateEnum::canceled()->value], 'description' => 'The current state of the operation.']], 'required' => [self::KEY_ID, self::KEY_STATE], 'additionalProperties' => \false],
        ]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return GenerativeAiOperationArrayShape
     */
    public function toArray(): array
    {
        $data = [self::KEY_ID => $this->id, self::KEY_STATE => $this->state->value];
        if ($this->result !== null) {
            $data[self::KEY_RESULT] = $this->result->toArray();
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
        static::validateFromArrayData($array, [self::KEY_ID, self::KEY_STATE]);
        $state = OperationStateEnum::from($array[self::KEY_STATE]);
        if ($state->isSucceeded()) {
            // If the operation has succeeded, it must have a result
            static::validateFromArrayData($array, [self::KEY_RESULT]);
        }
        $result = null;
        if (isset($array[self::KEY_RESULT])) {
            $result = GenerativeAiResult::fromArray($array[self::KEY_RESULT]);
        }
        return new self($array[self::KEY_ID], $state, $result);
    }
}
