<?php

declare (strict_types=1);
namespace WordPress\AiClient\Results\DTO;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
/**
 * Represents a single generated embedding vector.
 *
 * @since 1.4.0
 *
 * @phpstan-type EmbeddingList list<float|int>
 *
 * @implements IteratorAggregate<int, float|int>
 */
final class Embedding implements Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @var EmbeddingList
     */
    private array $values;
    /**
     * @var int The vector dimension count.
     */
    private int $dimensions;
    /**
     * Constructor.
     *
     * @since 1.4.0
     *
     * @param EmbeddingList $values The embedding vector values.
     * @param int $dimensions The vector dimension count.
     */
    public function __construct(array $values, int $dimensions)
    {
        if ($dimensions < 1) {
            throw new InvalidArgumentException('Embedding dimensions must be greater than zero');
        }
        if (!self::isEmbeddingList($values)) {
            if (!array_is_list($values)) {
                throw new InvalidArgumentException('Embedding values must be a list array.');
            }
            throw new InvalidArgumentException('Embedding values must be integers or floats.');
        }
        if (count($values) !== $dimensions) {
            throw new InvalidArgumentException('Embedding vector length must match dimensions.');
        }
        $this->values = $values;
        $this->dimensions = $dimensions;
    }
    /**
     * Checks whether the value is a list of integers or floats.
     *
     * @since 1.4.0
     *
     * @param mixed $values The value to check.
     * @return bool True if the value is an embedding list.
     *
     * @phpstan-assert-if-true EmbeddingList $values
     */
    private static function isEmbeddingList($values): bool
    {
        if (!is_array($values) || !array_is_list($values)) {
            return \false;
        }
        foreach ($values as $value) {
            if (!is_int($value) && !is_float($value)) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Gets the vector values.
     *
     * @since 1.4.0
     *
     * @return EmbeddingList The embedding vector values.
     */
    public function getValues(): array
    {
        return $this->values;
    }
    /**
     * Gets the vector dimension count.
     *
     * @since 1.4.0
     *
     * @return int The vector dimension count.
     */
    public function getDimensions(): int
    {
        return $this->dimensions;
    }
    /**
     * Gets the number of vector values.
     *
     * @since 1.4.0
     *
     * @return int The number of vector values.
     */
    public function count(): int
    {
        return $this->dimensions;
    }
    /**
     * Gets an iterator for the vector values.
     *
     * @since 1.4.0
     *
     * @return Traversable<int, float|int> The vector value iterator.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }
    /**
     * Gets the JSON schema for embedding vectors.
     *
     * @since 1.4.0
     *
     * @return array<string, mixed> The JSON schema.
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'array', 'items' => ['type' => 'number'], 'description' => 'Generated embedding vector values.'];
    }
    /**
     * Converts the embedding to an array.
     *
     * @since 1.4.0
     *
     * @return EmbeddingList The embedding vector values.
     */
    public function toArray(): array
    {
        return $this->values;
    }
    /**
     * Creates an embedding from an array.
     *
     * @since 1.4.0
     *
     * @param EmbeddingList $array The embedding vector values.
     * @param int $dimensions The vector dimension count.
     * @return self The embedding instance.
     */
    public static function fromArray(array $array, int $dimensions): self
    {
        return new self($array, $dimensions);
    }
    /**
     * Converts the embedding to a JSON-serializable value.
     *
     * @since 1.4.0
     *
     * @return EmbeddingList The embedding vector values.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
