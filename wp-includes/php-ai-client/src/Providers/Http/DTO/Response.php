<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Http\Collections\HeadersCollection;
/**
 * Represents an HTTP response.
 *
 * This class encapsulates HTTP response data that has been converted
 * from PSR-7 responses by the HTTP transporter.
 *
 * @since 0.1.0
 *
 * @phpstan-type ResponseArrayShape array{
 *     statusCode: int,
 *     headers: array<string, list<string>>,
 *     body?: string|null
 * }
 *
 * @extends AbstractDataTransferObject<ResponseArrayShape>
 */
class Response extends AbstractDataTransferObject
{
    public const KEY_STATUS_CODE = 'statusCode';
    public const KEY_HEADERS = 'headers';
    public const KEY_BODY = 'body';
    /**
     * @var int The HTTP status code.
     */
    protected int $statusCode;
    /**
     * @var HeadersCollection The response headers.
     */
    protected HeadersCollection $headers;
    /**
     * @var string|null The response body.
     */
    protected ?string $body;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param int $statusCode The HTTP status code.
     * @param array<string, string|list<string>> $headers The response headers.
     * @param string|null $body The response body.
     *
     * @throws InvalidArgumentException If the status code is invalid.
     */
    public function __construct(int $statusCode, array $headers, ?string $body = null)
    {
        if ($statusCode < 100 || $statusCode >= 600) {
            throw new InvalidArgumentException('Invalid HTTP status code: ' . $statusCode);
        }
        $this->statusCode = $statusCode;
        $this->headers = new HeadersCollection($headers);
        $this->body = $body;
    }
    /**
     * Creates a deep clone of this response.
     *
     * Clones the headers collection to ensure the cloned
     * response is independent of the original.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        // Clone headers collection
        $this->headers = clone $this->headers;
    }
    /**
     * Gets the HTTP status code.
     *
     * @since 0.1.0
     *
     * @return int The status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    /**
     * Gets the response headers.
     *
     * @since 0.1.0
     *
     * @return array<string, list<string>> The headers.
     */
    public function getHeaders(): array
    {
        return $this->headers->getAll();
    }
    /**
     * Gets a specific header value.
     *
     * @since 0.1.0
     *
     * @param string $name The header name (case-insensitive).
     * @return list<string>|null The header value(s) or null if not found.
     */
    public function getHeader(string $name): ?array
    {
        return $this->headers->get($name);
    }
    /**
     * Gets header values as a comma-separated string.
     *
     * @since 0.1.0
     *
     * @param string $name The header name (case-insensitive).
     * @return string|null The header values as a comma-separated string or null if not found.
     */
    public function getHeaderAsString(string $name): ?string
    {
        return $this->headers->getAsString($name);
    }
    /**
     * Gets the response body.
     *
     * @since 0.1.0
     *
     * @return string|null The body.
     */
    public function getBody(): ?string
    {
        return $this->body;
    }
    /**
     * Checks if the response has a header.
     *
     * @since 0.1.0
     *
     * @param string $name The header name.
     * @return bool True if the header exists, false otherwise.
     */
    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }
    /**
     * Checks if the response indicates success.
     *
     * @since 0.1.0
     *
     * @return bool True if status code is 2xx, false otherwise.
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
    /**
     * Gets the response data as an array.
     *
     * Attempts to decode the body as JSON. Returns null if the body
     * is empty or not valid JSON.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed>|null The decoded data or null.
     */
    public function getData(): ?array
    {
        if ($this->body === null || $this->body === '') {
            return null;
        }
        $data = json_decode($this->body, \true);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            return null;
        }
        /** @var array<string, mixed>|null $data */
        return is_array($data) ? $data : null;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_STATUS_CODE => ['type' => 'integer', 'minimum' => 100, 'maximum' => 599, 'description' => 'The HTTP status code.'], self::KEY_HEADERS => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']], 'description' => 'The response headers.'], self::KEY_BODY => ['type' => ['string', 'null'], 'description' => 'The response body.']], 'required' => [self::KEY_STATUS_CODE, self::KEY_HEADERS]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return ResponseArrayShape
     */
    public function toArray(): array
    {
        $data = [self::KEY_STATUS_CODE => $this->statusCode, self::KEY_HEADERS => $this->headers->getAll()];
        if ($this->body !== null) {
            $data[self::KEY_BODY] = $this->body;
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
        static::validateFromArrayData($array, [self::KEY_STATUS_CODE, self::KEY_HEADERS]);
        return new self($array[self::KEY_STATUS_CODE], $array[self::KEY_HEADERS], $array[self::KEY_BODY] ?? null);
    }
}
