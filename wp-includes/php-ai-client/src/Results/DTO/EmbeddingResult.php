<?php

declare (strict_types=1);
namespace WordPress\AiClient\Results\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Results\Contracts\ResultInterface;
/**
 * Represents the result of an embedding generation operation.
 *
 * @since 1.4.0
 *
 * @phpstan-import-type TokenUsageArrayShape from TokenUsage
 * @phpstan-import-type ProviderMetadataArrayShape from ProviderMetadata
 * @phpstan-import-type ModelMetadataArrayShape from ModelMetadata
 * @phpstan-import-type EmbeddingList from Embedding
 *
 * @phpstan-type EmbeddingResultArrayShape array{
 *     id: string,
 *     embeddings: list<EmbeddingList>,
 *     dimensions: int,
 *     tokenUsage: TokenUsageArrayShape,
 *     providerMetadata: ProviderMetadataArrayShape,
 *     modelMetadata: ModelMetadataArrayShape,
 *     additionalData?: array<string, mixed>
 * }
 *
 * @extends AbstractDataTransferObject<EmbeddingResultArrayShape>
 */
class EmbeddingResult extends AbstractDataTransferObject implements ResultInterface
{
    public const KEY_ID = 'id';
    public const KEY_EMBEDDINGS = 'embeddings';
    public const KEY_DIMENSIONS = 'dimensions';
    public const KEY_TOKEN_USAGE = 'tokenUsage';
    public const KEY_PROVIDER_METADATA = 'providerMetadata';
    public const KEY_MODEL_METADATA = 'modelMetadata';
    public const KEY_ADDITIONAL_DATA = 'additionalData';
    /**
     * @var string Unique identifier for this result.
     */
    private string $id;
    /**
     * @var list<Embedding>
     */
    private array $embeddings;
    /**
     * @var int The vector dimension count.
     */
    private int $dimensions;
    /**
     * @var TokenUsage Token usage statistics.
     */
    private \WordPress\AiClient\Results\DTO\TokenUsage $tokenUsage;
    /**
     * @var ProviderMetadata Provider metadata.
     */
    private ProviderMetadata $providerMetadata;
    /**
     * @var ModelMetadata Model metadata.
     */
    private ModelMetadata $modelMetadata;
    /**
     * @var array<string, mixed>
     */
    private array $additionalData;
    /**
     * Constructor.
     *
     * @since 1.4.0
     *
     * @param string $id Unique identifier for this result.
     * @param list<Embedding|EmbeddingList> $embeddings The generated embedding vectors.
     * @param int $dimensions The vector dimension count.
     * @param TokenUsage $tokenUsage Token usage statistics.
     * @param ProviderMetadata $providerMetadata Provider metadata.
     * @param ModelMetadata $modelMetadata Model metadata.
     * @param array<string, mixed> $additionalData Additional data.
     */
    public function __construct(string $id, array $embeddings, int $dimensions, \WordPress\AiClient\Results\DTO\TokenUsage $tokenUsage, ProviderMetadata $providerMetadata, ModelMetadata $modelMetadata, array $additionalData = [])
    {
        if (empty($embeddings)) {
            throw new InvalidArgumentException('At least one embedding must be provided');
        }
        if ($dimensions < 1) {
            throw new InvalidArgumentException('Embedding dimensions must be greater than zero');
        }
        $normalizedEmbeddings = [];
        foreach ($embeddings as $embedding) {
            if (!$embedding instanceof \WordPress\AiClient\Results\DTO\Embedding) {
                $embedding = new \WordPress\AiClient\Results\DTO\Embedding($embedding, $dimensions);
            } elseif ($embedding->getDimensions() !== $dimensions) {
                throw new InvalidArgumentException('Embedding vector length must match dimensions.');
            }
            $normalizedEmbeddings[] = $embedding;
        }
        $this->id = $id;
        $this->embeddings = $normalizedEmbeddings;
        $this->dimensions = $dimensions;
        $this->tokenUsage = $tokenUsage;
        $this->providerMetadata = $providerMetadata;
        $this->modelMetadata = $modelMetadata;
        $this->additionalData = $additionalData;
    }
    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function getId(): string
    {
        return $this->id;
    }
    /**
     * Gets the generated embedding vectors.
     *
     * @since 1.4.0
     *
     * @return list<Embedding> The embeddings.
     */
    public function getEmbeddings(): array
    {
        return $this->embeddings;
    }
    /**
     * Gets the first generated embedding vector.
     *
     * @since 1.4.0
     *
     * @return Embedding The first embedding.
     */
    public function getEmbedding(): \WordPress\AiClient\Results\DTO\Embedding
    {
        return $this->embeddings[0];
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
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function getTokenUsage(): \WordPress\AiClient\Results\DTO\TokenUsage
    {
        return $this->tokenUsage;
    }
    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function getProviderMetadata(): ProviderMetadata
    {
        return $this->providerMetadata;
    }
    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function getModelMetadata(): ModelMetadata
    {
        return $this->modelMetadata;
    }
    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
    /**
     * Gets the JSON schema for embedding results.
     *
     * @since 1.4.0
     *
     * @return array<string, mixed> The JSON schema.
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'Unique identifier for this result.'], self::KEY_EMBEDDINGS => ['type' => 'array', 'items' => \WordPress\AiClient\Results\DTO\Embedding::getJsonSchema(), 'description' => 'Generated embedding vectors.'], self::KEY_DIMENSIONS => ['type' => 'integer', 'minimum' => 1, 'description' => 'Embedding vector dimensions.'], self::KEY_TOKEN_USAGE => \WordPress\AiClient\Results\DTO\TokenUsage::getJsonSchema(), self::KEY_PROVIDER_METADATA => ProviderMetadata::getJsonSchema(), self::KEY_MODEL_METADATA => ModelMetadata::getJsonSchema(), self::KEY_ADDITIONAL_DATA => ['type' => 'object', 'additionalProperties' => \true, 'description' => 'Additional provider-specific data.']], 'required' => [self::KEY_ID, self::KEY_EMBEDDINGS, self::KEY_DIMENSIONS, self::KEY_TOKEN_USAGE, self::KEY_PROVIDER_METADATA, self::KEY_MODEL_METADATA]];
    }
    /**
     * Converts the embedding result to an array.
     *
     * @since 1.4.0
     *
     * @return EmbeddingResultArrayShape The embedding result array.
     */
    public function toArray(): array
    {
        $data = [self::KEY_ID => $this->id, self::KEY_EMBEDDINGS => array_map(static fn(\WordPress\AiClient\Results\DTO\Embedding $embedding): array => $embedding->toArray(), $this->embeddings), self::KEY_DIMENSIONS => $this->dimensions, self::KEY_TOKEN_USAGE => $this->tokenUsage->toArray(), self::KEY_PROVIDER_METADATA => $this->providerMetadata->toArray(), self::KEY_MODEL_METADATA => $this->modelMetadata->toArray()];
        if (!empty($this->additionalData)) {
            $data[self::KEY_ADDITIONAL_DATA] = $this->additionalData;
        }
        return $data;
    }
    /**
     * Creates an embedding result from an array.
     *
     * @since 1.4.0
     *
     * @param EmbeddingResultArrayShape $array The embedding result array.
     * @return self The embedding result instance.
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_ID, self::KEY_EMBEDDINGS, self::KEY_DIMENSIONS, self::KEY_TOKEN_USAGE, self::KEY_PROVIDER_METADATA, self::KEY_MODEL_METADATA]);
        return new self($array[self::KEY_ID], $array[self::KEY_EMBEDDINGS], $array[self::KEY_DIMENSIONS], \WordPress\AiClient\Results\DTO\TokenUsage::fromArray($array[self::KEY_TOKEN_USAGE]), ProviderMetadata::fromArray($array[self::KEY_PROVIDER_METADATA]), ModelMetadata::fromArray($array[self::KEY_MODEL_METADATA]), $array[self::KEY_ADDITIONAL_DATA] ?? []);
    }
}
