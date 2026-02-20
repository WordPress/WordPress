<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Models\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;
/**
 * Represents a supported configuration option for an AI model.
 *
 * This class defines an option that a model supports, including its name
 * and the values that are valid for that option.
 *
 * @since 0.1.0
 *
 * @phpstan-type SupportedOptionArrayShape array{
 *     name: string,
 *     supportedValues?: list<mixed>
 * }
 *
 * @extends AbstractDataTransferObject<SupportedOptionArrayShape>
 */
class SupportedOption extends AbstractDataTransferObject
{
    public const KEY_NAME = 'name';
    public const KEY_SUPPORTED_VALUES = 'supportedValues';
    /**
     * @var OptionEnum The option name.
     */
    protected OptionEnum $name;
    /**
     * @var list<mixed>|null The supported values for this option.
     */
    protected ?array $supportedValues;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param OptionEnum $name The option name.
     * @param list<mixed>|null $supportedValues The supported values for this option, or null if any value is supported.
     *
     * @throws InvalidArgumentException If supportedValues is not null and not a list.
     */
    public function __construct(OptionEnum $name, ?array $supportedValues = null)
    {
        if ($supportedValues !== null && !array_is_list($supportedValues)) {
            throw new InvalidArgumentException('Supported values must be a list array.');
        }
        $this->name = $name;
        $this->supportedValues = $supportedValues;
    }
    /**
     * Gets the option name.
     *
     * @since 0.1.0
     *
     * @return OptionEnum The option name.
     */
    public function getName(): OptionEnum
    {
        return $this->name;
    }
    /**
     * Checks if a value is supported for this option.
     *
     * @since 0.1.0
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is supported, false otherwise.
     */
    public function isSupportedValue($value): bool
    {
        // If supportedValues is null, any value is supported
        if ($this->supportedValues === null) {
            return \true;
        }
        // If the value is an array, consider it a set (i.e. order doesn't matter).
        if (is_array($value)) {
            sort($value);
            foreach ($this->supportedValues as $supportedValue) {
                if (!is_array($supportedValue)) {
                    continue;
                }
                sort($supportedValue);
                if ($value === $supportedValue) {
                    return \true;
                }
            }
            return \false;
        }
        return in_array($value, $this->supportedValues, \true);
    }
    /**
     * Gets the supported values for this option.
     *
     * @since 0.1.0
     *
     * @return list<mixed>|null The supported values, or null if any value is supported.
     */
    public function getSupportedValues(): ?array
    {
        return $this->supportedValues;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_NAME => ['type' => 'string', 'enum' => OptionEnum::getValues(), 'description' => 'The option name.'], self::KEY_SUPPORTED_VALUES => ['type' => 'array', 'items' => ['oneOf' => [['type' => 'string'], ['type' => 'number'], ['type' => 'boolean'], ['type' => 'null'], ['type' => 'array'], ['type' => 'object']]], 'description' => 'The supported values for this option.']], 'required' => [self::KEY_NAME]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return SupportedOptionArrayShape
     */
    public function toArray(): array
    {
        $data = [self::KEY_NAME => $this->name->value];
        if ($this->supportedValues !== null) {
            /** @var list<mixed> $supportedValues */
            $supportedValues = $this->supportedValues;
            $data[self::KEY_SUPPORTED_VALUES] = $supportedValues;
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
        static::validateFromArrayData($array, [self::KEY_NAME]);
        return new self(OptionEnum::from($array[self::KEY_NAME]), $array[self::KEY_SUPPORTED_VALUES] ?? null);
    }
}
