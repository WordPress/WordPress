<?php

declare (strict_types=1);
namespace WordPress\AiClient\Tools\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
/**
 * Represents a function declaration for AI models.
 *
 * This DTO describes a function that can be called by the AI model,
 * including its name, description, parameter schema, and optional annotations.
 *
 * @since 0.1.0
 *
 * @phpstan-type FunctionDeclarationArrayShape array{
 *     name: string,
 *     description: string,
 *     parameters?: array<string, mixed>,
 *     annotations?: array<string, mixed>
 * }
 *
 * @extends AbstractDataTransferObject<FunctionDeclarationArrayShape>
 */
class FunctionDeclaration extends AbstractDataTransferObject
{
    public const KEY_NAME = 'name';
    public const KEY_DESCRIPTION = 'description';
    public const KEY_PARAMETERS = 'parameters';
    public const KEY_ANNOTATIONS = 'annotations';
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
     * @var array<string, mixed> Open-ended annotations which may be consumed by Providers.
     */
    private array $annotations;
    /**
     * Constructor.
     *
     * @since 0.1.0
     * @since 1.5.0 Adds the optional $annotations parameter.
     *
     * @param string $name The name of the function.
     * @param string $description A description of what the function does.
     * @param array<string, mixed>|null $parameters The JSON schema for the function parameters.
     * @param array<string, mixed> $annotations Optional annotations with JSON-serializable values.
     */
    public function __construct(string $name, string $description, ?array $parameters = null, array $annotations = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = $parameters;
        $this->annotations = $annotations;
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
     * Gets the function annotations.
     *
     * @since 1.5.0
     *
     * @return array<string, mixed> The annotations, or an empty array if none were provided.
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_NAME => ['type' => 'string', 'description' => 'The name of the function.'], self::KEY_DESCRIPTION => ['type' => 'string', 'description' => 'A description of what the function does.'], self::KEY_PARAMETERS => ['type' => 'object', 'description' => 'The JSON schema for the function parameters.', 'additionalProperties' => \true], self::KEY_ANNOTATIONS => ['type' => 'object', 'description' => 'Optional annotations interpreted by consumers.', 'additionalProperties' => \true]], 'required' => [self::KEY_NAME, self::KEY_DESCRIPTION]];
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
        if ($this->annotations !== []) {
            $data[self::KEY_ANNOTATIONS] = $this->annotations;
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
        return new self($array[self::KEY_NAME], $array[self::KEY_DESCRIPTION], $array[self::KEY_PARAMETERS] ?? null, $array[self::KEY_ANNOTATIONS] ?? []);
    }
}
