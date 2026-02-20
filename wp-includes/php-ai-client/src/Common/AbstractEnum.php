<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common;

use BadMethodCallException;
use JsonSerializable;
use ReflectionClass;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
/**
 * Abstract base class for enum-like behavior in PHP 7.4.
 *
 * This class provides enum-like functionality for PHP versions that don't support native enums.
 * Child classes should define uppercase snake_case constants for enum values.
 *
 * @example
 * class PersonEnum extends AbstractEnum {
 *     public const FIRST_NAME = 'first';
 *     public const LAST_NAME = 'last';
 * }
 *
 * // Usage:
 * $enum = PersonEnum::from('first'); // Creates instance with value 'first'
 * $enum = PersonEnum::tryFrom('invalid'); // Returns null
 * $enum = PersonEnum::firstName(); // Creates instance with value 'first'
 * $enum->name; // 'FIRST_NAME'
 * $enum->value; // 'first'
 * $enum->equals('first'); // Returns true
 * $enum->is(PersonEnum::firstName()); // Returns true
 * PersonEnum::cases(); // Returns array of all enum instances
 *
 * @property-read string $value The value of the enum instance.
 * @property-read string $name The name of the enum constant.
 *
 * @since 0.1.0
 */
abstract class AbstractEnum implements JsonSerializable
{
    /**
     * @var string The value of the enum instance.
     */
    private string $value;
    /**
     * @var string The name of the enum constant.
     */
    private string $name;
    /**
     * @var array<string, array<string, string>> Cache for reflection data.
     */
    private static array $cache = [];
    /**
     * @var array<string, array<string, self>> Cache for enum instances.
     */
    private static array $instances = [];
    /**
     * Constructor is private to ensure instances are created through static methods.
     *
     * @since 0.1.0
     *
     * @param string $value The enum value.
     * @param string $name The constant name.
     */
    final private function __construct(string $value, string $name)
    {
        $this->value = $value;
        $this->name = $name;
    }
    /**
     * Provides read-only access to properties.
     *
     * @since 0.1.0
     *
     * @param string $property The property name.
     * @return mixed The property value.
     * @throws BadMethodCallException If property doesn't exist.
     */
    final public function __get(string $property)
    {
        if ($property === 'value' || $property === 'name') {
            return $this->{$property};
        }
        throw new BadMethodCallException(sprintf('Property %s::%s does not exist', static::class, $property));
    }
    /**
     * Prevents property modification.
     *
     * @since 0.1.0
     *
     * @param string $property The property name.
     * @param mixed $value The value to set.
     * @throws BadMethodCallException Always, as enum properties are read-only.
     */
    final public function __set(string $property, $value): void
    {
        throw new BadMethodCallException(sprintf('Cannot modify property %s::%s - enum properties are read-only', static::class, $property));
    }
    /**
     * Creates an enum instance from a value, throws exception if invalid.
     *
     * @since 0.1.0
     *
     * @param string $value The enum value.
     * @return static The enum instance.
     * @throws InvalidArgumentException If the value is not valid.
     */
    final public static function from(string $value): self
    {
        $instance = self::tryFrom($value);
        if ($instance === null) {
            throw new InvalidArgumentException(sprintf('%s is not a valid backing value for enum %s', $value, static::class));
        }
        return $instance;
    }
    /**
     * Tries to create an enum instance from a value, returns null if invalid.
     *
     * @since 0.1.0
     *
     * @param string $value The enum value.
     * @return static|null The enum instance or null.
     */
    final public static function tryFrom(string $value): ?self
    {
        $constants = static::getConstants();
        foreach ($constants as $name => $constantValue) {
            if ($constantValue === $value) {
                return self::getInstance($constantValue, $name);
            }
        }
        return null;
    }
    /**
     * Gets all enum cases.
     *
     * @since 0.1.0
     *
     * @return static[] Array of all enum instances.
     */
    final public static function cases(): array
    {
        $cases = [];
        $constants = static::getConstants();
        foreach ($constants as $name => $value) {
            $cases[] = self::getInstance($value, $name);
        }
        return $cases;
    }
    /**
     * Checks if this enum has the same value as the given value.
     *
     * @since 0.1.0
     *
     * @param string|self $other The value or enum to compare.
     * @return bool True if values are equal.
     */
    final public function equals($other): bool
    {
        if ($other instanceof self) {
            return $this->is($other);
        }
        return $this->value === $other;
    }
    /**
     * Checks if this enum is the same instance type and value as another enum.
     *
     * @since 0.1.0
     *
     * @param self $other The other enum to compare.
     * @return bool True if enums are identical.
     */
    final public function is(self $other): bool
    {
        return $this === $other;
        // Since we're using singletons, we can use identity comparison
    }
    /**
     * Gets all valid values for this enum.
     *
     * @since 0.1.0
     *
     * @return string[] List of all enum values.
     */
    final public static function getValues(): array
    {
        return array_values(static::getConstants());
    }
    /**
     * Checks if a value is valid for this enum.
     *
     * @since 0.1.0
     *
     * @param string $value The value to check.
     * @return bool True if value is valid.
     */
    final public static function isValidValue(string $value): bool
    {
        return in_array($value, self::getValues(), \true);
    }
    /**
     * Gets or creates a singleton instance for the given value and name.
     *
     * @since 0.1.0
     *
     * @param string $value The enum value.
     * @param string $name The constant name.
     * @return static The enum instance.
     */
    private static function getInstance(string $value, string $name): self
    {
        $className = static::class;
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = [];
        }
        if (!isset(self::$instances[$className][$name])) {
            $instance = new $className($value, $name);
            self::$instances[$className][$name] = $instance;
        }
        /** @var static */
        return self::$instances[$className][$name];
    }
    /**
     * Gets all constants for this enum class.
     *
     * @since 0.1.0
     *
     * @return array<string, string> Map of constant names to values.
     * @throws RuntimeException If invalid constant found.
     */
    final protected static function getConstants(): array
    {
        $className = static::class;
        if (!isset(self::$cache[$className])) {
            self::$cache[$className] = static::determineClassEnumerations($className);
        }
        return self::$cache[$className];
    }
    /**
     * Determines the class enumerations by reflecting on class constants.
     *
     * This method can be overridden by subclasses to customize how
     * enumerations are determined (e.g., to add dynamic constants).
     *
     * @since 0.1.0
     *
     * @param class-string $className The fully qualified class name.
     * @return array<string, string> Map of constant names to values.
     * @throws RuntimeException If invalid constant found.
     */
    protected static function determineClassEnumerations(string $className): array
    {
        $reflection = new ReflectionClass($className);
        $constants = $reflection->getConstants();
        // Validate all constants
        $enumConstants = [];
        foreach ($constants as $name => $value) {
            // Check if constant name follows uppercase snake_case pattern
            if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $name)) {
                throw new RuntimeException(sprintf('Invalid enum constant name "%s" in %s. Constants must be UPPER_SNAKE_CASE.', $name, $className));
            }
            // Check if value is valid type
            if (!is_string($value)) {
                throw new RuntimeException(sprintf('Invalid enum value type for constant %s::%s. ' . 'Only string values are allowed, %s given.', $className, $name, gettype($value)));
            }
            $enumConstants[$name] = $value;
        }
        return $enumConstants;
    }
    /**
     * Handles dynamic method calls for enum checking.
     *
     * @since 0.1.0
     *
     * @param string $name The method name.
     * @param array<mixed> $arguments The method arguments.
     * @return bool True if the enum value matches.
     * @throws BadMethodCallException If the method doesn't exist.
     */
    final public function __call(string $name, array $arguments): bool
    {
        // Handle is* methods
        if (str_starts_with($name, 'is')) {
            $constantName = self::camelCaseToConstant(substr($name, 2));
            $constants = static::getConstants();
            if (isset($constants[$constantName])) {
                return $this->value === $constants[$constantName];
            }
        }
        throw new BadMethodCallException(sprintf('Method %s::%s does not exist', static::class, $name));
    }
    /**
     * Handles static method calls for enum creation.
     *
     * @since 0.1.0
     *
     * @param string $name The method name.
     * @param array<mixed> $arguments The method arguments.
     * @return static The enum instance.
     * @throws BadMethodCallException If the method doesn't exist.
     */
    final public static function __callStatic(string $name, array $arguments): self
    {
        $constantName = self::camelCaseToConstant($name);
        $constants = static::getConstants();
        if (isset($constants[$constantName])) {
            return self::getInstance($constants[$constantName], $constantName);
        }
        throw new BadMethodCallException(sprintf('Method %s::%s does not exist', static::class, $name));
    }
    /**
     * Converts camelCase to CONSTANT_CASE.
     *
     * @since 0.1.0
     *
     * @param string $camelCase The camelCase string.
     * @return string The CONSTANT_CASE version.
     */
    private static function camelCaseToConstant(string $camelCase): string
    {
        $snakeCase = preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCase);
        if ($snakeCase === null) {
            return strtoupper($camelCase);
        }
        return strtoupper($snakeCase);
    }
    /**
     * Returns string representation of the enum.
     *
     * @since 0.1.0
     *
     * @return string The enum value.
     */
    final public function __toString(): string
    {
        return $this->value;
    }
    /**
     * Converts the enum to a JSON-serializable format.
     *
     * @since 0.1.0
     *
     * @return string The enum value.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value;
    }
}
