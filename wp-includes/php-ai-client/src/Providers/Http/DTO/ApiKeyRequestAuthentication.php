<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Providers\Http\Contracts\RequestAuthenticationInterface;
/**
 * Class for HTTP request authentication using an API key.
 *
 * @since 0.1.0
 *
 * @phpstan-type ApiKeyRequestAuthenticationArrayShape array{
 *     apiKey: string
 * }
 *
 * @extends AbstractDataTransferObject<ApiKeyRequestAuthenticationArrayShape>
 */
class ApiKeyRequestAuthentication extends AbstractDataTransferObject implements RequestAuthenticationInterface
{
    public const KEY_API_KEY = 'apiKey';
    /**
     * @var string The API key used for authentication.
     */
    protected string $apiKey;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $apiKey The API key used for authentication.
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function authenticateRequest(\WordPress\AiClient\Providers\Http\DTO\Request $request): \WordPress\AiClient\Providers\Http\DTO\Request
    {
        // Add the API key to the request headers.
        return $request->withHeader('Authorization', 'Bearer ' . $this->apiKey);
    }
    /**
     * Gets the API key.
     *
     * @since 0.1.0
     *
     * @return string The API key.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @since 0.1.0
     *
     * @return ApiKeyRequestAuthenticationArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_API_KEY => $this->apiKey];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_API_KEY]);
        return new self($array[self::KEY_API_KEY]);
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_API_KEY => ['type' => 'string', 'title' => 'API Key', 'description' => 'The API key used for authentication.']], 'required' => [self::KEY_API_KEY]];
    }
}
