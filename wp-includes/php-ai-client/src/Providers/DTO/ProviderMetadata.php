<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Enums\ProviderTypeEnum;
use WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod;
/**
 * Represents metadata about an AI provider.
 *
 * This class contains information about an AI provider, including its
 * unique identifier, display name, and type (cloud, server, or client).
 *
 * @since 0.1.0
 * @since 1.2.0 Added optional description property.
 * @since 1.3.0 Added optional logoPath property.
 *
 * @phpstan-type ProviderMetadataArrayShape array{
 *     id: string,
 *     name: string,
 *     description?: ?string,
 *     type: string,
 *     credentialsUrl?: ?string,
 *     authenticationMethod?: ?string,
 *     logoPath?: ?string
 * }
 *
 * @extends AbstractDataTransferObject<ProviderMetadataArrayShape>
 */
class ProviderMetadata extends AbstractDataTransferObject
{
    public const KEY_ID = 'id';
    public const KEY_NAME = 'name';
    public const KEY_DESCRIPTION = 'description';
    public const KEY_TYPE = 'type';
    public const KEY_CREDENTIALS_URL = 'credentialsUrl';
    public const KEY_AUTHENTICATION_METHOD = 'authenticationMethod';
    public const KEY_LOGO_PATH = 'logoPath';
    /**
     * @var string The provider's unique identifier.
     */
    protected string $id;
    /**
     * @var string The provider's display name.
     */
    protected string $name;
    /**
     * @var string|null The provider's description.
     */
    protected ?string $description;
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
     * @var string|null The full path to the provider's logo image file.
     */
    protected ?string $logoPath;
    /**
     * Constructor.
     *
     * @since 0.1.0
     * @since 1.2.0 Added optional $description parameter.
     * @since 1.3.0 Added optional $logoPath parameter.
     *
     * @param string $id The provider's unique identifier.
     * @param string $name The provider's display name.
     * @param ProviderTypeEnum $type The provider type.
     * @param string|null $credentialsUrl The URL where users can get credentials.
     * @param RequestAuthenticationMethod|null $authenticationMethod The authentication method.
     * @param string|null $description The provider's description.
     * @param string|null $logoPath The full path to the provider's logo image file.
     * @throws InvalidArgumentException If the provider ID contains invalid characters.
     */
    public function __construct(string $id, string $name, ProviderTypeEnum $type, ?string $credentialsUrl = null, ?RequestAuthenticationMethod $authenticationMethod = null, ?string $description = null, ?string $logoPath = null)
    {
        if (!preg_match('/^[a-z0-9\-_]+$/', $id)) {
            throw new InvalidArgumentException(sprintf(
                // phpcs:ignore Generic.Files.LineLength.TooLong
                'Invalid provider ID "%s". Only lowercase alphanumeric characters, hyphens, and underscores are allowed.',
                $id
            ));
        }
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->credentialsUrl = $credentialsUrl;
        $this->authenticationMethod = $authenticationMethod;
        $this->logoPath = $logoPath;
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
     * Gets the provider's description.
     *
     * @since 1.2.0
     *
     * @return string|null The provider description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
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
     * Gets the full path to the provider's logo image file.
     *
     * @since 1.3.0
     *
     * @return string|null The full path to the logo image file.
     */
    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     * @since 1.2.0 Added description to schema.
     * @since 1.3.0 Added logoPath to schema.
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ID => ['type' => 'string', 'description' => 'The provider\'s unique identifier.'], self::KEY_NAME => ['type' => 'string', 'description' => 'The provider\'s display name.'], self::KEY_DESCRIPTION => ['type' => 'string', 'description' => 'The provider\'s description.'], self::KEY_TYPE => ['type' => 'string', 'enum' => ProviderTypeEnum::getValues(), 'description' => 'The provider type (cloud, server, or client).'], self::KEY_CREDENTIALS_URL => ['type' => 'string', 'description' => 'The URL where users can get credentials.'], self::KEY_AUTHENTICATION_METHOD => ['type' => ['string', 'null'], 'enum' => array_merge(RequestAuthenticationMethod::getValues(), [null]), 'description' => 'The authentication method.'], self::KEY_LOGO_PATH => ['type' => 'string', 'description' => 'The full path to the provider\'s logo image file.']], 'required' => [self::KEY_ID, self::KEY_NAME, self::KEY_TYPE]];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     * @since 1.2.0 Added description to output.
     * @since 1.3.0 Added logoPath to output.
     *
     * @return ProviderMetadataArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_ID => $this->id, self::KEY_NAME => $this->name, self::KEY_DESCRIPTION => $this->description, self::KEY_TYPE => $this->type->value, self::KEY_CREDENTIALS_URL => $this->credentialsUrl, self::KEY_AUTHENTICATION_METHOD => $this->authenticationMethod ? $this->authenticationMethod->value : null, self::KEY_LOGO_PATH => $this->logoPath];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     * @since 1.2.0 Added description support.
     * @since 1.3.0 Added logoPath support.
     */
    public static function fromArray(array $array): self
    {
        static::validateFromArrayData($array, [self::KEY_ID, self::KEY_NAME, self::KEY_TYPE]);
        return new self($array[self::KEY_ID], $array[self::KEY_NAME], ProviderTypeEnum::from($array[self::KEY_TYPE]), $array[self::KEY_CREDENTIALS_URL] ?? null, isset($array[self::KEY_AUTHENTICATION_METHOD]) ? RequestAuthenticationMethod::from($array[self::KEY_AUTHENTICATION_METHOD]) : null, $array[self::KEY_DESCRIPTION] ?? null, $array[self::KEY_LOGO_PATH] ?? null);
    }
}
