<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Providers\Enums\ProviderTypeEnum;
use WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod;
/**
 * Represents metadata about an AI provider.
 *
 * This class contains information about an AI provider, including its
 * unique identifier, display name, and type (cloud, server, or client).
 *
 * @since 0.1.0
 *
 * @phpstan-type ProviderMetadataArrayShape array{
 *     id: string,
 *     name: string,
 *     type: string,
 *     credentialsUrl?: ?string,
 *     authenticationMethod?: ?string
 * }
 *
 * @extends AbstractDataTransferObject<ProviderMetadataArrayShape>
 */
class ProviderMetadata extends AbstractDataTransferObject
{
    public const KEY_ID = 'id';
    public const KEY_NAME = 'name';
    public const KEY_TYPE = 'type';
    public const KEY_CREDENTIALS_URL = 'credentialsUrl';
    public const KEY_AUTHENTICATION_METHOD = 'authenticationMethod';
    /**
     * @var string The provider's unique identifier.
     */
    protected string $id;
    /**
     * @var string The provider's display name.
     */
    protected string $name;
    /**
     * @var ProviderTypeEnum The provider type.
     */
    protected ProviderTypeEnum $type;
    /**
     * @var string|null The URL where users can get credentials.
     */
    protected ?string $credentialsUrl;
    /**
     * @var RequestAuthenticationMethod|null The authentication method.
     */
    protected ?RequestAuthenticationMethod $authenticationMethod;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $id The provider's unique identifier.
     * @param string $name The provider's display name.
     * @param ProviderTypeEnum $type The provider type.
     * @param string|null $credentialsUrl The URL where users can get credentials.
     * @param RequestAuthenticationMethod|null $authenticationMethod The authentication method.
     */
    public function __construct(string $id, string $name, ProviderTypeEnum $type, ?string $credentialsUrl = null, ?RequestAuthenticationMethod $authenticationMethod = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->credentialsUrl = $credentialsUrl;
        $this->authenticationMethod = $authenticationMethod;
    }
    /**
     * Gets the provider's unique identifier.
     *
     * @since 0.1.0
     *
     * @return string The provider ID.
     */
    public function getId(): string
    {
        return $this->id;
    }
    /**
     * Gets the provider's display name.
     *
     * @since 0.1.0
     *
     * @return string The provider name.
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Gets the provider type.
     *
     * @since 0.1.0
     *
     * @return ProviderTypeEnum The provider type.
     */
    public function getType(): ProviderTypeEnum
    {
        return $this->type;
    }
    /**
     * Gets the credentials URL.
     *
     * @since 0.1.0
     *
     * @return string|null The credentials URL.
     */
    public function getCredentialsUrl(): ?string
    {
        return $this->credentialsUrl;
    }
    /**
     * Gets the authentication method.
     *
     * @since 0.4.0
     *
     * @return RequestAuthenticationMethod|null The authentication method.
     */
    public function getAuthenticationMethod(): ?RequestAuthenticationMethod
    {
        return $this->authenticationMethod;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'The provider\'s unique identifier.'], self::KEY_NAME => ['type' => 'string', 'description' => 'The provider\'s display name.'], self::KEY_TYPE => ['type' => 'string', 'enum' => ProviderTypeEnum::getValues(), 'description' => 'The provider type (cloud, server, or client).'], self::KEY_CREDENTIALS_URL => ['type' => 'string', 'description' => 'The URL where users can get credentials.'], self::KEY_AUTHENTICATION_METHOD => ['type' => ['string', 'null'], 'enum' => array_merge(RequestAuthenticationMethod::getValues(), [null]), 'description' => 'The authentication method.']], 'required' => [self::KEY_ID, self::KEY_NAME, self::KEY_TYPE]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return ProviderMetadataArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_ID => $this->id, self::KEY_NAME => $this->name, self::KEY_TYPE => $this->type->value, self::KEY_CREDENTIALS_URL => $this->credentialsUrl, self::KEY_AUTHENTICATION_METHOD => $this->authenticationMethod ? $this->authenticationMethod->value : null];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_ID, self::KEY_NAME, self::KEY_TYPE]);
        return new self($array[self::KEY_ID], $array[self::KEY_NAME], ProviderTypeEnum::from($array[self::KEY_TYPE]), $array[self::KEY_CREDENTIALS_URL] ?? null, isset($array[self::KEY_AUTHENTICATION_METHOD]) ? RequestAuthenticationMethod::from($array[self::KEY_AUTHENTICATION_METHOD]) : null);
    }
}
