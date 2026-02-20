<?php

declare (strict_types=1);
namespace WordPress\AiClient\Tools\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
/**
 * Represents a function call request from an AI model.
 *
 * This DTO encapsulates information about a function that the AI model
 * wants to invoke, including the function name and its arguments.
 *
 * @since 0.1.0
 *
 * @phpstan-type FunctionCallArrayShape array{id?: string, name?: string, args?: mixed}
 *
 * @extends AbstractDataTransferObject<FunctionCallArrayShape>
 */
class FunctionCall extends AbstractDataTransferObject
{
    public const KEY_ID = 'id';
    public const KEY_NAME = 'name';
    public const KEY_ARGS = 'args';
    /**
     * @var string|null Unique identifier for this function call.
     */
    private ?string $id;
    /**
     * @var string|null The name of the function to call.
     */
    private ?string $name;
    /**
     * @var mixed The arguments to pass to the function.
     */
    private $args;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string|null $id Unique identifier for this function call.
     * @param string|null $name The name of the function to call.
     * @param mixed $args The arguments to pass to the function.
     * @throws InvalidArgumentException If neither id nor name is provided.
     */
    public function __construct(?string $id = null, ?string $name = null, $args = null)
    {
        if ($id === null && $name === null) {
            throw new InvalidArgumentException('At least one of id or name must be provided.');
        }
        $this->id = $id;
        $this->name = $name;
        $this->args = $args;
    }
    /**
     * Gets the function call ID.
     *
     * @since 0.1.0
     *
     * @return string|null The function call ID.
     */
    public function getId(): ?string
    {
        return $this->id;
    }
    /**
     * Gets the function name.
     *
     * @since 0.1.0
     *
     * @return string|null The function name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    /**
     * Gets the function arguments.
     *
     * @since 0.1.0
     *
     * @return mixed The function arguments.
     */
    public function getArgs()
    {
        return $this->args;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'Unique identifier for this function call.'], self::KEY_NAME => ['type' => 'string', 'description' => 'The name of the function to call.'], self::KEY_ARGS => ['type' => ['string', 'number', 'boolean', 'object', 'array', 'null'], 'description' => 'The arguments to pass to the function.']], 'oneOf' => [['required' => [self::KEY_ID]], ['required' => [self::KEY_NAME]]]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return FunctionCallArrayShape
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->id !== null) {
            $data[self::KEY_ID] = $this->id;
        }
        if ($this->name !== null) {
            $data[self::KEY_NAME] = $this->name;
        }
        if ($this->args !== null) {
            $data[self::KEY_ARGS] = $this->args;
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
        return new self($array[self::KEY_ID] ?? null, $array[self::KEY_NAME] ?? null, $array[self::KEY_ARGS] ?? null);
    }
}
