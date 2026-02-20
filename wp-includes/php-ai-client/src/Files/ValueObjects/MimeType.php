<?php

declare (strict_types=1);
namespace WordPress\AiClient\Files\ValueObjects;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
/**
 * Value object representing a MIME type.
 *
 * This immutable value object encapsulates MIME type validation and
 * provides convenient methods for checking MIME type categories.
 *
 * @since 0.1.0
 */
final class MimeType
{
    /**
     * @var string The MIME type value.
     */
    private string $value;
    /**
     * Common MIME type mappings for file extensions.
     *
     * @var array<string, string>
     */
    private static array $extensionMap = [
        // Text
        'txt' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'csv' => 'text/csv',
        'md' => 'text/markdown',
        // Images
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        // Documents
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        // Archives
        'zip' => 'application/zip',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip',
        'rar' => 'application/x-rar-compressed',
        '7z' => 'application/x-7z-compressed',
        // Audio
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        'm4a' => 'audio/m4a',
        'aac' => 'audio/aac',
        // Video
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',
        // Fonts
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        // Other
        'php' => 'application/x-httpd-php',
        'sh' => 'application/x-sh',
        'exe' => 'application/x-msdownload',
    ];
    /**
     * Document MIME types.
     *
     * @var array<string>
     */
    private static array $documentTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet'];
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $value The MIME type value.
     * @throws InvalidArgumentException If the MIME type is invalid.
     */
    public function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException(sprintf('Invalid MIME type: %s', $value));
        }
        $this->value = strtolower($value);
    }
    /**
     * Gets the primary known file extension for this MIME type.
     *
     * @since 0.1.0
     *
     * @return string The file extension (without the dot).
     * @throws InvalidArgumentException If no known extension exists for this MIME type.
     */
    public function toExtension(): string
    {
        // Reverse lookup for the MIME type to find the extension.
        $extension = array_search($this->value, self::$extensionMap, \true);
        if ($extension === \false) {
            throw new InvalidArgumentException(sprintf('No known extension for MIME type: %s', $this->value));
        }
        return $extension;
    }
    /**
     * Creates a MimeType from a file extension.
     *
     * @since 0.1.0
     *
     * @param string $extension The file extension (without the dot).
     * @return self The MimeType instance.
     * @throws InvalidArgumentException If the extension is not recognized.
     */
    public static function fromExtension(string $extension): self
    {
        $extension = strtolower($extension);
        if (!isset(self::$extensionMap[$extension])) {
            throw new InvalidArgumentException(sprintf('Unknown file extension: %s', $extension));
        }
        return new self(self::$extensionMap[$extension]);
    }
    /**
     * Checks if a MIME type string is valid.
     *
     * @since 0.1.0
     *
     * @param string $mimeType The MIME type to validate.
     * @return bool True if valid.
     */
    public static function isValid(string $mimeType): bool
    {
        // Basic MIME type validation: type/subtype
        return (bool) preg_match('/^[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_+.]*\/[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_+.]*$/', $mimeType);
    }
    /**
     * Checks if this MIME type is a specific type.
     *
     * This method returns true when the stored MIME type begins with the
     * given prefix. For example, `"audio"` matches `"audio/mpeg"`.
     *
     * @since 0.1.0
     *
     * @param string $mimeType The MIME type prefix to check (e.g., "audio", "image").
     * @return bool True if this MIME type is of the specified type.
     */
    public function isType(string $mimeType): bool
    {
        return str_starts_with($this->value, strtolower($mimeType) . '/');
    }
    /**
     * Checks if this is an image MIME type.
     *
     * @since 0.1.0
     *
     * @return bool True if this is an image type.
     */
    public function isImage(): bool
    {
        return $this->isType('image');
    }
    /**
     * Checks if this is an audio MIME type.
     *
     * @since 0.1.0
     *
     * @return bool True if this is an audio type.
     */
    public function isAudio(): bool
    {
        return $this->isType('audio');
    }
    /**
     * Checks if this is a video MIME type.
     *
     * @since 0.1.0
     *
     * @return bool True if this is a video type.
     */
    public function isVideo(): bool
    {
        return $this->isType('video');
    }
    /**
     * Checks if this is a text MIME type.
     *
     * @since 0.1.0
     *
     * @return bool True if this is a text type.
     */
    public function isText(): bool
    {
        return $this->isType('text');
    }
    /**
     * Checks if this is a document MIME type.
     *
     * @since 0.1.0
     *
     * @return bool True if this is a document type.
     */
    public function isDocument(): bool
    {
        return in_array($this->value, self::$documentTypes, \true);
    }
    /**
     * Checks if this MIME type equals another.
     *
     * @since 0.1.0
     *
     * @param self|string $other The other MIME type to compare.
     * @return bool True if equal.
     * @throws InvalidArgumentException If the other MIME type is invalid.
     */
    public function equals($other): bool
    {
        if ($other instanceof self) {
            return $this->value === $other->value;
        }
        if (is_string($other)) {
            return $this->value === strtolower($other);
        }
        throw new InvalidArgumentException(sprintf('Invalid MIME type comparison: %s', gettype($other)));
    }
    /**
     * Gets the string representation of the MIME type.
     *
     * @since 0.1.0
     *
     * @return string The MIME type value.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
