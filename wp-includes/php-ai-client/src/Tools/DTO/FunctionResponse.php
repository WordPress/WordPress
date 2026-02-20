<?php

declare (strict_types=1);
namespace WordPress\AiClient\Tools\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
/**
 * Represents a response to a function call.
 *
 * This DTO encapsulates the result of executing a function that was
 * requested by the AI model through a FunctionCall.
 *
 * @since 0.1.0
 *
 * @phpstan-type FunctionResponseArrayShape array{id: string, name: string, response: mixed}
 *
 * @extends AbstractDataTransferObject<FunctionResponseArrayShape>
 */
class FunctionResponse extends AbstractDataTransferObject
{
    public const KEY_ID = 'id';
    public const KEY_NAME = 'name';
    public const KEY_RESPONSE = 'response';
    /**
     * @var string The ID of the function call this is responding to.
     */
    private string $id;
    /**
     * @var string The name of the function that was called.
     */
    private string $name;
    /**
     * @var mixed The response data from the function.
     */
    private $response;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $id The ID of the function call this is responding to.
     * @param string $name The name of the function that was called.
     * @param mixed $response The response data from the function.
     */
    public function __construct(string $id, string $name, $response)
    {
        $this->id = $id;
        $this->name = $name;
        $this->response = $response;
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
     * Gets the function response.
     *
     * @since 0.1.0
     *
     * @return mixed The response data.
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'The ID of the function call this is responding to.'], self::KEY_NAME => ['type' => 'string', 'description' => 'The name of the function that was called.'], self::KEY_RESPONSE => ['type' => ['string', 'number', 'boolean', 'object', 'array', 'null'], 'description' => 'The response data from the function.']], 'oneOf' => [['required' => [self::KEY_RESPONSE, self::KEY_ID]], ['required' => [self::KEY_RESPONSE, self::KEY_NAME]]]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return FunctionResponseArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_ID => $this->id, self::KEY_NAME => $this->name, self::KEY_RESPONSE => $this->response];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_RESPONSE]);
        // Validate that at least one of id or name is provided
        if (!array_key_exists(self::KEY_ID, $array) && !array_key_exists(self::KEY_NAME, $array)) {
            throw new InvalidArgumentException('At least one of id or name must be provided.');
        }
        return new self($array[self::KEY_ID] ?? null, $array[self::KEY_NAME] ?? null, $array[self::KEY_RESPONSE]);
    }
}
