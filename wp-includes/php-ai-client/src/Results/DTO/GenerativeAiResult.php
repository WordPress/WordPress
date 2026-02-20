<?php

declare (strict_types=1);
namespace WordPress\AiClient\Results\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Files\DTO\File;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Results\Contracts\ResultInterface;
/**
 * Represents the result of a generative AI operation.
 *
 * This DTO contains the generated candidates along with usage statistics
 * and metadata from the AI provider.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type CandidateArrayShape from Candidate
 * @phpstan-import-type TokenUsageArrayShape from TokenUsage
 * @phpstan-import-type ProviderMetadataArrayShape from ProviderMetadata
 * @phpstan-import-type ModelMetadataArrayShape from ModelMetadata
 *
 * @phpstan-type GenerativeAiResultArrayShape array{
 *     id: string,
 *     candidates: array<CandidateArrayShape>,
 *     tokenUsage: TokenUsageArrayShape,
 *     providerMetadata: ProviderMetadataArrayShape,
 *     modelMetadata: ModelMetadataArrayShape,
 *     additionalData?: array<string, mixed>
 * }
 *
 * @extends AbstractDataTransferObject<GenerativeAiResultArrayShape>
 */
class GenerativeAiResult extends AbstractDataTransferObject implements ResultInterface
{
    public const KEY_ID = 'id';
    public const KEY_CANDIDATES = 'candidates';
    public const KEY_TOKEN_USAGE = 'tokenUsage';
    public const KEY_PROVIDER_METADATA = 'providerMetadata';
    public const KEY_MODEL_METADATA = 'modelMetadata';
    public const KEY_ADDITIONAL_DATA = 'additionalData';
    /**
     * @var string Unique identifier for this result.
     */
    private string $id;
    /**
     * @var Candidate[] The generated candidates.
     */
    private array $candidates;
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
     * @var array<string, mixed> Additional data.
     */
    private array $additionalData;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $id Unique identifier for this result.
     * @param Candidate[] $candidates The generated candidates.
     * @param TokenUsage $tokenUsage Token usage statistics.
     * @param ProviderMetadata $providerMetadata Provider metadata.
     * @param ModelMetadata $modelMetadata Model metadata.
     * @param array<string, mixed> $additionalData Additional data.
     * @throws InvalidArgumentException If no candidates provided.
     */
    public function __construct(string $id, array $candidates, \WordPress\AiClient\Results\DTO\TokenUsage $tokenUsage, ProviderMetadata $providerMetadata, ModelMetadata $modelMetadata, array $additionalData = [])
    {
        if (empty($candidates)) {
            throw new InvalidArgumentException('At least one candidate must be provided');
        }
        $this->id = $id;
        $this->candidates = $candidates;
        $this->tokenUsage = $tokenUsage;
        $this->providerMetadata = $providerMetadata;
        $this->modelMetadata = $modelMetadata;
        $this->additionalData = $additionalData;
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
     * Gets the generated candidates.
     *
     * @since 0.1.0
     *
     * @return Candidate[] The candidates.
     */
    public function getCandidates(): array
    {
        return $this->candidates;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function getTokenUsage(): \WordPress\AiClient\Results\DTO\TokenUsage
    {
        return $this->tokenUsage;
    }
    /**
     * Gets the provider metadata.
     *
     * @since 0.1.0
     *
     * @return ProviderMetadata The provider metadata.
     */
    public function getProviderMetadata(): ProviderMetadata
    {
        return $this->providerMetadata;
    }
    /**
     * Gets the model metadata.
     *
     * @since 0.1.0
     *
     * @return ModelMetadata The model metadata.
     */
    public function getModelMetadata(): ModelMetadata
    {
        return $this->modelMetadata;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
    /**
     * Gets the total number of candidates.
     *
     * @since 0.1.0
     *
     * @return int The total number of candidates.
     */
    public function getCandidateCount(): int
    {
        return count($this->candidates);
    }
    /**
     * Checks if the result has multiple candidates.
     *
     * @since 0.1.0
     *
     * @return bool True if there are multiple candidates, false otherwise.
     */
    public function hasMultipleCandidates(): bool
    {
        return $this->getCandidateCount() > 1;
    }
    /**
     * Converts the first candidate to text.
     *
     * Only text from the content channel is considered. Text within model thought or reasoning is ignored.
     *
     * @since 0.1.0
     *
     * @return string The text content.
     * @throws RuntimeException If no text content.
     */
    public function toText(): string
    {
        $message = $this->candidates[0]->getMessage();
        foreach ($message->getParts() as $part) {
            $channel = $part->getChannel();
            $text = $part->getText();
            if ($channel->isContent() && $text !== null) {
                return $text;
            }
        }
        throw new RuntimeException('No text content found in first candidate');
    }
    /**
     * Converts the first candidate to a file.
     *
     * Only files from the content channel are considered. Files within model thought or reasoning are ignored.
     *
     * @since 0.1.0
     *
     * @return File The file.
     * @throws RuntimeException If no file content.
     */
    public function toFile(): File
    {
        $message = $this->candidates[0]->getMessage();
        foreach ($message->getParts() as $part) {
            $channel = $part->getChannel();
            $file = $part->getFile();
            if ($channel->isContent() && $file !== null) {
                return $file;
            }
        }
        throw new RuntimeException('No file content found in first candidate');
    }
    /**
     * Converts the first candidate to an image file.
     *
     * @since 0.1.0
     *
     * @return File The image file.
     * @throws RuntimeException If no image content.
     */
    public function toImageFile(): File
    {
        $file = $this->toFile();
        if (!$file->isImage()) {
            throw new RuntimeException(sprintf('File is not an image. MIME type: %s', $file->getMimeType()));
        }
        return $file;
    }
    /**
     * Converts the first candidate to an audio file.
     *
     * @since 0.1.0
     *
     * @return File The audio file.
     * @throws RuntimeException If no audio content.
     */
    public function toAudioFile(): File
    {
        $file = $this->toFile();
        if (!$file->isAudio()) {
            throw new RuntimeException(sprintf('File is not an audio file. MIME type: %s', $file->getMimeType()));
        }
        return $file;
    }
    /**
     * Converts the first candidate to a video file.
     *
     * @since 0.1.0
     *
     * @return File The video file.
     * @throws RuntimeException If no video content.
     */
    public function toVideoFile(): File
    {
        $file = $this->toFile();
        if (!$file->isVideo()) {
            throw new RuntimeException(sprintf('File is not a video file. MIME type: %s', $file->getMimeType()));
        }
        return $file;
    }
    /**
     * Converts the first candidate to a message.
     *
     * @since 0.1.0
     *
     * @return Message The message.
     */
    public function toMessage(): Message
    {
        return $this->candidates[0]->getMessage();
    }
    /**
     * Converts all candidates to text.
     *
     * @since 0.1.0
     *
     * @return list<string> Array of text content.
     */
    public function toTexts(): array
    {
        $texts = [];
        foreach ($this->candidates as $candidate) {
            $message = $candidate->getMessage();
            foreach ($message->getParts() as $part) {
                $channel = $part->getChannel();
                $text = $part->getText();
                if ($channel->isContent() && $text !== null) {
                    $texts[] = $text;
                    break;
                }
            }
        }
        return $texts;
    }
    /**
     * Converts all candidates to files.
     *
     * @since 0.1.0
     *
     * @return list<File> Array of files.
     */
    public function toFiles(): array
    {
        $files = [];
        foreach ($this->candidates as $candidate) {
            $message = $candidate->getMessage();
            foreach ($message->getParts() as $part) {
                $channel = $part->getChannel();
                $file = $part->getFile();
                if ($channel->isContent() && $file !== null) {
                    $files[] = $file;
                    break;
                }
            }
        }
        return $files;
    }
    /**
     * Converts all candidates to image files.
     *
     * @since 0.1.0
     *
     * @return list<File> Array of image files.
     */
    public function toImageFiles(): array
    {
        return array_values(array_filter($this->toFiles(), fn(File $file) => $file->isImage()));
    }
    /**
     * Converts all candidates to audio files.
     *
     * @since 0.1.0
     *
     * @return list<File> Array of audio files.
     */
    public function toAudioFiles(): array
    {
        return array_values(array_filter($this->toFiles(), fn(File $file) => $file->isAudio()));
    }
    /**
     * Converts all candidates to video files.
     *
     * @since 0.1.0
     *
     * @return list<File> Array of video files.
     */
    public function toVideoFiles(): array
    {
        return array_values(array_filter($this->toFiles(), fn(File $file) => $file->isVideo()));
    }
    /**
     * Converts all candidates to messages.
     *
     * @since 0.1.0
     *
     * @return list<Message> Array of messages.
     */
    public function toMessages(): array
    {
        return array_values(array_map(fn(\WordPress\AiClient\Results\DTO\Candidate $candidate) => $candidate->getMessage(), $this->candidates));
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'Unique identifier for this result.'], self::KEY_CANDIDATES => ['type' => 'array', 'items' => \WordPress\AiClient\Results\DTO\Candidate::getJsonSchema(), 'minItems' => 1, 'description' => 'The generated candidates.'], self::KEY_TOKEN_USAGE => \WordPress\AiClient\Results\DTO\TokenUsage::getJsonSchema(), self::KEY_PROVIDER_METADATA => ProviderMetadata::getJsonSchema(), self::KEY_MODEL_METADATA => ModelMetadata::getJsonSchema(), self::KEY_ADDITIONAL_DATA => ['type' => 'object', 'additionalProperties' => \true, 'description' => 'Additional data included in the API response.']], 'required' => [self::KEY_ID, self::KEY_CANDIDATES, self::KEY_TOKEN_USAGE, self::KEY_PROVIDER_METADATA, self::KEY_MODEL_METADATA]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return GenerativeAiResultArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_ID => $this->id, self::KEY_CANDIDATES => array_map(fn(\WordPress\AiClient\Results\DTO\Candidate $candidate) => $candidate->toArray(), $this->candidates), self::KEY_TOKEN_USAGE => $this->tokenUsage->toArray(), self::KEY_PROVIDER_METADATA => $this->providerMetadata->toArray(), self::KEY_MODEL_METADATA => $this->modelMetadata->toArray(), self::KEY_ADDITIONAL_DATA => $this->additionalData];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_ID, self::KEY_CANDIDATES, self::KEY_TOKEN_USAGE, self::KEY_PROVIDER_METADATA, self::KEY_MODEL_METADATA]);
        $candidates = array_map(fn(array $candidateData) => \WordPress\AiClient\Results\DTO\Candidate::fromArray($candidateData), $array[self::KEY_CANDIDATES]);
        return new self($array[self::KEY_ID], $candidates, \WordPress\AiClient\Results\DTO\TokenUsage::fromArray($array[self::KEY_TOKEN_USAGE]), ProviderMetadata::fromArray($array[self::KEY_PROVIDER_METADATA]), ModelMetadata::fromArray($array[self::KEY_MODEL_METADATA]), $array[self::KEY_ADDITIONAL_DATA] ?? []);
    }
    /**
     * Performs a deep clone of the result.
     *
     * This method ensures that all nested objects (candidates, token usage, metadata)
     * are cloned to prevent modifications to the cloned result from affecting the original.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        $clonedCandidates = [];
        foreach ($this->candidates as $candidate) {
            $clonedCandidates[] = clone $candidate;
        }
        $this->candidates = $clonedCandidates;
        $this->tokenUsage = clone $this->tokenUsage;
        $this->providerMetadata = clone $this->providerMetadata;
        $this->modelMetadata = clone $this->modelMetadata;
    }
}
