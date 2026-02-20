<?php

declare (strict_types=1);
namespace WordPress\AiClient\Tools\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
/**
 * Represents a function declaration for AI models.
 *
 * This DTO describes a function that can be called by the AI model,
 * including its name, description, and parameter schema.
 *
 * @since 0.1.0
 *
 * @phpstan-type FunctionDeclarationArrayShape array{
 *     name: string,
 *     description: string,
 *     parameters?: array<string, mixed>
 * }
 *
 * @extends AbstractDataTransferObject<FunctionDeclarationArrayShape>
 */
class FunctionDeclaration extends AbstractDataTransferObject
{
    public const KEY_NAME = 'name';
    public const KEY_DESCRIPTION = 'description';
    public const KEY_PARAMETERS = 'parameters';
    /**
     * @var string The name of the function.
     */
    private string $name;
    /**
     * @var string A description of what the function does.
     */
    private string $description;
    /**
     * @var array<string, mixed>|null The JSON schema for the function parameters.
     */
    private ?array $parameters;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $name The name of the function.
     * @param string $description A description of what the function does.
     * @param array<string, mixed>|null $parameters The JSON schema for the function parameters.
     */
    public function __construct(string $name, string $description, ?array $parameters = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = $parameters;
    }
    /**
     * Gets the function name.
     *
     * @since 0.1.0
     *
     * @return string The function name.
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Gets the function description.
     *
     * @since 0.1.0
     *
     * @return string The function description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    /**
     * Gets the function parameters schema.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed>|null The parameters schema.
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_NAME => ['type' => 'string', 'description' => 'The name of the function.'], self::KEY_DESCRIPTION => ['type' => 'string', 'description' => 'A description of what the function does.'], self::KEY_PARAMETERS => ['type' => 'object', 'description' => 'The JSON schema for the function parameters.', 'additionalProperties' => \true]], 'required' => [self::KEY_NAME, self::KEY_DESCRIPTION]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return FunctionDeclarationArrayShape
     */
    public function toArray(): array
    {
        $data = [self::KEY_NAME => $this->name, self::KEY_DESCRIPTION => $this->description];
        if ($this->parameters !== null) {
            $data[self::KEY_PARAMETERS] = $this->parameters;
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
        static::validateFromArrayData($array, [self::KEY_NAME, self::KEY_DESCRIPTION]);
        return new self($array[self::KEY_NAME], $array[self::KEY_DESCRIPTION], $array[self::KEY_PARAMETERS] ?? null);
    }
}
