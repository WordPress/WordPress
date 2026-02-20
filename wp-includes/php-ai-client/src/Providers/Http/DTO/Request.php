<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\DTO;

use JsonException;
use WordPress\AiClientDependencies\Psr\Http\Message\RequestInterface;
use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Http\Collections\HeadersCollection;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
/**
 * Represents an HTTP request.
 *
 * This class encapsulates HTTP request data that can be converted
 * to PSR-7 requests by the HTTP transporter.
 *
 * @since 0.1.0
 *
 * @phpstan-import-type RequestOptionsArrayShape from RequestOptions
 * @phpstan-type RequestArrayShape array{
 *     method: string,
 *     uri: string,
 *     headers: array<string, list<string>>,
 *     body?: string|null,
 *     options?: RequestOptionsArrayShape
 * }
 *
 * @extends AbstractDataTransferObject<RequestArrayShape>
 */
class Request extends AbstractDataTransferObject
{
    public const KEY_METHOD = 'method';
    public const KEY_URI = 'uri';
    public const KEY_HEADERS = 'headers';
    public const KEY_BODY = 'body';
    public const KEY_OPTIONS = 'options';
    /**
     * @var HttpMethodEnum The HTTP method.
     */
    protected HttpMethodEnum $method;
    /**
     * @var string The request URI.
     */
    protected string $uri;
    /**
     * @var HeadersCollection The request headers.
     */
    protected HeadersCollection $headers;
    /**
     * @var array<string, mixed>|null The request data (for query params or form data).
     */
    protected ?array $data = null;
    /**
     * @var string|null The request body (raw string content).
     */
    protected ?string $body = null;
    /**
     * @var RequestOptions|null Request transport options.
     */
    protected ?\WordPress\AiClient\Providers\Http\DTO\RequestOptions $options = null;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param HttpMethodEnum $method The HTTP method.
     * @param string $uri The request URI.
     * @param array<string, string|list<string>> $headers The request headers.
     * @param string|array<string, mixed>|null $data The request data.
     * @param RequestOptions|null $options The request transport options.
     *
     * @throws InvalidArgumentException If the URI is empty.
     */
    public function __construct(HttpMethodEnum $method, string $uri, array $headers = [], $data = null, ?\WordPress\AiClient\Providers\Http\DTO\RequestOptions $options = null)
    {
        if (empty($uri)) {
            throw new InvalidArgumentException('URI cannot be empty.');
        }
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = new HeadersCollection($headers);
        // Separate data and body based on type
        if (is_string($data)) {
            $this->body = $data;
        } elseif (is_array($data)) {
            $this->data = $data;
        }
        $this->options = $options;
    }
    /**
     * Creates a deep clone of this request.
     *
     * Clones the headers collection and request options to ensure
     * the cloned request is independent of the original.
     * The HTTP method enum is immutable and can be safely shared.
     *
     * @since 0.4.2
     */
    public function __clone()
    {
        // Clone headers collection
        $this->headers = clone $this->headers;
        // Clone request options if present (contains only primitives)
        if ($this->options !== null) {
            $this->options = clone $this->options;
        }
        // Note: $method is an immutable enum and can be safely shared
    }
    /**
     * Gets the HTTP method.
     *
     * @since 0.1.0
     *
     * @return HttpMethodEnum The HTTP method.
     */
    public function getMethod(): HttpMethodEnum
    {
        return $this->method;
    }
    /**
     * Gets the request URI.
     *
     * For GET requests with array data, appends the data as query parameters.
     *
     * @since 0.1.0
     *
     * @return string The URI.
     */
    public function getUri(): string
    {
        // If GET request with data, append as query parameters
        if ($this->method === HttpMethodEnum::GET() && $this->data !== null && !empty($this->data)) {
            $separator = str_contains($this->uri, '?') ? '&' : '?';
            return $this->uri . $separator . http_build_query($this->data);
        }
        return $this->uri;
    }
    /**
     * Gets the request headers.
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
     * @return string|null The header values as a comma-separated string, or null if not found.
     */
    public function getHeaderAsString(string $name): ?string
    {
        return $this->headers->getAsString($name);
    }
    /**
     * Checks if a header exists.
     *
     * @since 0.1.0
     *
     * @param string $name The header name (case-insensitive).
     * @return bool True if the header exists, false otherwise.
     */
    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }
    /**
     * Gets the request body.
     *
     * For GET requests, returns null.
     * For POST/PUT/PATCH requests:
     * - If body is set, returns it as-is
     * - If data is set and Content-Type is JSON, returns JSON-encoded data
     * - If data is set and Content-Type is form, returns URL-encoded data
     *
     * @since 0.1.0
     *
     * @return string|null The body.
     * @throws JsonException If the data cannot be encoded to JSON.
     */
    public function getBody(): ?string
    {
        // GET requests don't have a body
        if (!$this->method->hasBody()) {
            return null;
        }
        // If body is set, return it as-is
        if ($this->body !== null) {
            return $this->body;
        }
        // If data is set, encode based on content type
        if ($this->data !== null) {
            $contentType = $this->getContentType();
            // JSON encoding
            if ($contentType !== null && stripos($contentType, 'application/json') !== \false) {
                return json_encode($this->data, \JSON_THROW_ON_ERROR);
            }
            // Default to URL encoding for forms
            return http_build_query($this->data);
        }
        return null;
    }
    /**
     * Gets the Content-Type header value.
     *
     * @since 0.1.0
     *
     * @return string|null The Content-Type header value or null if not set.
     */
    private function getContentType(): ?string
    {
        $values = $this->getHeader('Content-Type');
        return $values !== null ? $values[0] : null;
    }
    /**
     * Returns a new instance with the specified header.
     *
     * @since 0.1.0
     *
     * @param string $name The header name.
     * @param string|list<string> $value The header value(s).
     * @return self A new instance with the header.
     */
    public function withHeader(string $name, $value): self
    {
        $newHeaders = $this->headers->withHeader($name, $value);
        $new = clone $this;
        $new->headers = $newHeaders;
        return $new;
    }
    /**
     * Returns a new instance with the specified data.
     *
     * @since 0.1.0
     *
     * @param string|array<string, mixed> $data The request data.
     * @return self A new instance with the data.
     */
    public function withData($data): self
    {
        $new = clone $this;
        if (is_string($data)) {
            $new->body = $data;
            $new->data = null;
        } elseif (is_array($data)) {
            $new->data = $data;
            $new->body = null;
        } else {
            $new->data = null;
            $new->body = null;
        }
        return $new;
    }
    /**
     * Gets the request data array.
     *
     * @since 0.1.0
     *
     * @return array<string, mixed>|null The request data array.
     */
    public function getData(): ?array
    {
        return $this->data;
    }
    /**
     * Gets the request options.
     *
     * @since 0.2.0
     *
     * @return RequestOptions|null Request transport options when configured.
     */
    public function getOptions(): ?\WordPress\AiClient\Providers\Http\DTO\RequestOptions
    {
        return $this->options;
    }
    /**
     * Returns a new instance with the specified request options.
     *
     * @since 0.2.0
     *
     * @param RequestOptions|null $options The request options to apply.
     * @return self A new instance with the options.
     */
    public function withOptions(?\WordPress\AiClient\Providers\Http\DTO\RequestOptions $options): self
    {
        $new = clone $this;
        $new->options = $options;
        return $new;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_METHOD => ['type' => 'string', 'description' => 'The HTTP method.'], self::KEY_URI => ['type' => 'string', 'description' => 'The request URI.'], self::KEY_HEADERS => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']], 'description' => 'The request headers.'], self::KEY_BODY => ['type' => ['string'], 'description' => 'The request body.'], self::KEY_OPTIONS => \WordPress\AiClient\Providers\Http\DTO\RequestOptions::getJsonSchema()], 'required' => [self::KEY_METHOD, self::KEY_URI, self::KEY_HEADERS]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return RequestArrayShape
     */
    public function toArray(): array
    {
        $array = [
            self::KEY_METHOD => $this->method->value,
            self::KEY_URI => $this->getUri(),
            // Include query params if GET with data
            self::KEY_HEADERS => $this->headers->getAll(),
        ];
        // Include body if present (getBody() handles the conversion)
        $body = $this->getBody();
        if ($body !== null) {
            $array[self::KEY_BODY] = $body;
        }
        if ($this->options !== null) {
            $optionsArray = $this->options->toArray();
            if (!empty($optionsArray)) {
                $array[self::KEY_OPTIONS] = $optionsArray;
            }
        }
        return $array;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_METHOD, self::KEY_URI, self::KEY_HEADERS]);
        return new self(HttpMethodEnum::from($array[self::KEY_METHOD]), $array[self::KEY_URI], $array[self::KEY_HEADERS] ?? [], $array[self::KEY_BODY] ?? null, isset($array[self::KEY_OPTIONS]) ? \WordPress\AiClient\Providers\Http\DTO\RequestOptions::fromArray($array[self::KEY_OPTIONS]) : null);
    }
    /**
     * Creates a Request instance from a PSR-7 RequestInterface.
     *
     * @since 0.2.0
     *
     * @param RequestInterface $psrRequest The PSR-7 request to convert.
     * @return self A new Request instance.
     * @throws InvalidArgumentException If the HTTP method is not supported.
     */
    public static function fromPsrRequest(RequestInterface $psrRequest): self
    {
        $method = HttpMethodEnum::from($psrRequest->getMethod());
        $uri = (string) $psrRequest->getUri();
        // Convert PSR-7 headers to array format expected by our constructor
        /** @var array<string, list<string>> $headers */
        $headers = $psrRequest->getHeaders();
        // Get body content
        $body = $psrRequest->getBody()->getContents();
        $bodyOrData = !empty($body) ? $body : null;
        return new self($method, $uri, $headers, $bodyOrData);
    }
}
