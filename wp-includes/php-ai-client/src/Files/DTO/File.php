<?php

declare (strict_types=1);
namespace WordPress\AiClient\Files\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Files\Enums\FileTypeEnum;
use WordPress\AiClient\Files\ValueObjects\MimeType;
/**
 * Represents a file in the AI client.
 *
 * This DTO automatically detects whether a file is a URL, base64 data, or local file path
 * and handles them appropriately.
 *
 * @since 0.1.0
 *
 * @phpstan-type FileArrayShape array{
 *     fileType: string,
 *     url?: string,
 *     mimeType: string,
 *     base64Data?: string
 * }
 *
 * @extends AbstractDataTransferObject<FileArrayShape>
 */
class File extends AbstractDataTransferObject
{
    public const KEY_FILE_TYPE = 'fileType';
    public const KEY_MIME_TYPE = 'mimeType';
    public const KEY_URL = 'url';
    public const KEY_BASE64_DATA = 'base64Data';
    /**
     * @var MimeType The MIME type of the file.
     */
    private MimeType $mimeType;
    /**
     * @var FileTypeEnum The type of file storage.
     */
    private FileTypeEnum $fileType;
    /**
     * @var string|null The URL for remote files.
     */
    private ?string $url = null;
    /**
     * @var string|null The base64 data for inline files.
     */
    private ?string $base64Data = null;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $file The file string (URL, base64 data, or local path).
     * @param string|null $mimeType The MIME type of the file (optional).
     * @throws InvalidArgumentException If the file format is invalid or MIME type cannot be determined.
     */
    public function __construct(string $file, ?string $mimeType = null)
    {
        // Detect and process the file type (will set MIME type if possible)
        $this->detectAndProcessFile($file, $mimeType);
    }
    /**
     * Detects the file type and processes it accordingly.
     *
     * @since 0.1.0
     *
     * @param string $file The file string to process.
     * @param string|null $providedMimeType The explicitly provided MIME type.
     * @throws InvalidArgumentException If the file format is invalid or MIME type cannot be determined.
     */
    private function detectAndProcessFile(string $file, ?string $providedMimeType): void
    {
        // Check if it's a URL
        if ($this->isUrl($file)) {
            $this->fileType = FileTypeEnum::remote();
            $this->url = $file;
            $this->mimeType = $this->determineMimeType($providedMimeType, null, $file);
            return;
        }
        // Data URI pattern.
        $dataUriPattern = '/^data:(?:([a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_+.]*\/[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_+.]*' . '(?:;[a-zA-Z0-9\-]+=[a-zA-Z0-9\-]+)*)?;)?base64,([A-Za-z0-9+\/]*={0,2})$/';
        // Check if it's a data URI.
        if (preg_match($dataUriPattern, $file, $matches)) {
            $this->fileType = FileTypeEnum::inline();
            $this->base64Data = $matches[2];
            // Extract just the base64 data
            $extractedMimeType = empty($matches[1]) ? null : $matches[1];
            $this->mimeType = $this->determineMimeType($providedMimeType, $extractedMimeType, null);
            return;
        }
        // Check if it's a local file path (before base64 check)
        if (file_exists($file) && is_file($file)) {
            $this->fileType = FileTypeEnum::inline();
            $this->base64Data = $this->convertFileToBase64($file);
            $this->mimeType = $this->determineMimeType($providedMimeType, null, $file);
            return;
        }
        // Check if it's plain base64
        if (preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $file)) {
            if ($providedMimeType === null) {
                throw new InvalidArgumentException('MIME type is required when providing plain base64 data without data URI format.');
            }
            $this->fileType = FileTypeEnum::inline();
            $this->base64Data = $file;
            $this->mimeType = new MimeType($providedMimeType);
            return;
        }
        throw new InvalidArgumentException('Invalid file provided. Expected URL, base64 data, or valid local file path.');
    }
    /**
     * Checks if a string is a valid URL.
     *
     * @since 0.1.0
     *
     * @param string $string The string to check.
     * @return bool True if the string is a URL.
     */
    private function isUrl(string $string): bool
    {
        return filter_var($string, \FILTER_VALIDATE_URL) !== \false && preg_match('/^https?:\/\//i', $string);
    }
    /**
     * Converts a local file to base64.
     *
     * @since 0.1.0
     *
     * @param string $filePath The path to the local file.
     * @return string The base64-encoded file data.
     * @throws RuntimeException If the file cannot be read.
     */
    private function convertFileToBase64(string $filePath): string
    {
        $fileContent = @file_get_contents($filePath);
        if ($fileContent === \false) {
            throw new RuntimeException(sprintf('Unable to read file: %s', $filePath));
        }
        return base64_encode($fileContent);
    }
    /**
     * Gets the file type.
     *
     * @since 0.1.0
     *
     * @return FileTypeEnum The file type.
     */
    public function getFileType(): FileTypeEnum
    {
        return $this->fileType;
    }
    /**
     * Checks if the file is an inline file.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is inline (base64/data URI).
     */
    public function isInline(): bool
    {
        return $this->fileType->isInline();
    }
    /**
     * Checks if the file is a remote file.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is remote (URL).
     */
    public function isRemote(): bool
    {
        return $this->fileType->isRemote();
    }
    /**
     * Gets the URL for remote files.
     *
     * @since 0.1.0
     *
     * @return string|null The URL, or null if not a remote file.
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
    /**
     * Gets the base64-encoded data for inline files.
     *
     * @since 0.1.0
     *
     * @return string|null The plain base64-encoded data (without data URI prefix), or null if not an inline file.
     */
    public function getBase64Data(): ?string
    {
        return $this->base64Data;
    }
    /**
     * Gets the data as a data URI for inline files.
     *
     * @since 0.1.0
     *
     * @return string|null The data URI in format: data:[mimeType];base64,[data], or null if not an inline file.
     */
    public function getDataUri(): ?string
    {
        if ($this->base64Data === null) {
            return null;
        }
        return sprintf('data:%s;base64,%s', $this->getMimeType(), $this->base64Data);
    }
    /**
     * Gets the MIME type of the file as a string.
     *
     * @since 0.1.0
     *
     * @return string The MIME type string value.
     */
    public function getMimeType(): string
    {
        return (string) $this->mimeType;
    }
    /**
     * Gets the MIME type object.
     *
     * @since 0.1.0
     *
     * @return MimeType The MIME type object.
     */
    public function getMimeTypeObject(): MimeType
    {
        return $this->mimeType;
    }
    /**
     * Checks if the file is a video.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is a video.
     */
    public function isVideo(): bool
    {
        return $this->mimeType->isVideo();
    }
    /**
     * Checks if the file is an image.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is an image.
     */
    public function isImage(): bool
    {
        return $this->mimeType->isImage();
    }
    /**
     * Checks if the file is audio.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is audio.
     */
    public function isAudio(): bool
    {
        return $this->mimeType->isAudio();
    }
    /**
     * Checks if the file is text.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is text.
     */
    public function isText(): bool
    {
        return $this->mimeType->isText();
    }
    /**
     * Checks if the file is a document.
     *
     * @since 0.1.0
     *
     * @return bool True if the file is a document.
     */
    public function isDocument(): bool
    {
        return $this->mimeType->isDocument();
    }
    /**
     * Checks if the file is a specific MIME type.
     *
     * @since 0.1.0
     *
     * @param string $type The mime type to check (e.g. 'image', 'text', 'video', 'audio').
     *
     * @return bool True if the file is of the specified type.
     */
    public function isMimeType(string $type): bool
    {
        return $this->mimeType->isType($type);
    }
    /**
     * Determines the MIME type from various sources.
     *
     * @since 0.1.0
     *
     * @param string|null $providedMimeType The explicitly provided MIME type.
     * @param string|null $extractedMimeType The MIME type extracted from data URI.
     * @param string|null $pathOrUrl The file path or URL to extract extension from.
     * @return MimeType The determined MIME type.
     * @throws InvalidArgumentException If MIME type cannot be determined.
     */
    private function determineMimeType(?string $providedMimeType, ?string $extractedMimeType, ?string $pathOrUrl): MimeType
    {
        // Prefer explicitly provided MIME type
        if ($providedMimeType !== null) {
            return new MimeType($providedMimeType);
        }
        // Use extracted MIME type from data URI
        if ($extractedMimeType !== null) {
            return new MimeType($extractedMimeType);
        }
        // Try to determine from file extension
        if ($pathOrUrl !== null) {
            $parsedUrl = parse_url($pathOrUrl);
            $path = $parsedUrl['path'] ?? $pathOrUrl;
            // Remove query string and fragment if present
            $cleanPath = strtok($path, '?#');
            if ($cleanPath === \false) {
                $cleanPath = $path;
            }
            $extension = pathinfo($cleanPath, \PATHINFO_EXTENSION);
            if (!empty($extension)) {
                try {
                    return MimeType::fromExtension($extension);
                } catch (InvalidArgumentException $e) {
                    // Extension not recognized, continue to error
                    unset($e);
                }
            }
        }
        throw new InvalidArgumentException('Unable to determine MIME type. Please provide it explicitly.');
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'oneOf' => [['properties' => [self::KEY_FILE_TYPE => ['type' => 'string', 'const' => FileTypeEnum::REMOTE, 'description' => 'The file type.'], self::KEY_MIME_TYPE => ['type' => 'string', 'description' => 'The MIME type of the file.', 'pattern' => '^[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_+.]*\/[a-zA-Z0-9]' . '[a-zA-Z0-9!#$&\-\^_+.]*$'], self::KEY_URL => ['type' => 'string', 'format' => 'uri', 'description' => 'The URL to the remote file.']], 'required' => [self::KEY_FILE_TYPE, self::KEY_MIME_TYPE, self::KEY_URL]], ['properties' => [self::KEY_FILE_TYPE => ['type' => 'string', 'const' => FileTypeEnum::INLINE, 'description' => 'The file type.'], self::KEY_MIME_TYPE => ['type' => 'string', 'description' => 'The MIME type of the file.', 'pattern' => '^[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_+.]*\/[a-zA-Z0-9]' . '[a-zA-Z0-9!#$&\-\^_+.]*$'], self::KEY_BASE64_DATA => ['type' => 'string', 'description' => 'The base64-encoded file data.']], 'required' => [self::KEY_FILE_TYPE, self::KEY_MIME_TYPE, self::KEY_BASE64_DATA]]]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return FileArrayShape
     */
    public function toArray(): array
    {
        $data = [self::KEY_FILE_TYPE => $this->fileType->value, self::KEY_MIME_TYPE => $this->getMimeType()];
        if ($this->url !== null) {
            $data[self::KEY_URL] = $this->url;
        } elseif (!$this->fileType->isRemote() && $this->base64Data !== null) {
            $data[self::KEY_BASE64_DATA] = $this->base64Data;
        } else {
            throw new RuntimeException('File requires either url or base64Data. This should not be a possible condition.');
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
        static::validateFromArrayData($array, [self::KEY_FILE_TYPE]);
        // Check which properties are set to determine how to construct the File
        $mimeType = $array[self::KEY_MIME_TYPE] ?? null;
        if (isset($array[self::KEY_URL])) {
            return new self($array[self::KEY_URL], $mimeType);
        } elseif (isset($array[self::KEY_BASE64_DATA])) {
            return new self($array[self::KEY_BASE64_DATA], $mimeType);
        } else {
            throw new InvalidArgumentException('File requires either url or base64Data.');
        }
    }
    /**
     * Performs a deep clone of the file.
     *
     * This method ensures that the MimeType value object is cloned to prevent
     * any shared references between the original and cloned file.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        $this->mimeType = clone $this->mimeType;
    }
}
