<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Enums;

use WordPress\AiClient\Common\AbstractEnum;
use WordPress\AiClient\Common\Contracts\WithArrayTransformationInterface;
use WordPress\AiClient\Providers\Http\Contracts\RequestAuthenticationInterface;
use WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication;
/**
 * Enum for request authentication methods.
 *
 * @since 0.4.0
 *
 * @method static self apiKey() Creates an instance for API_KEY method.
 * @method bool isApiKey() Checks if the method is API_KEY.
 */
class RequestAuthenticationMethod extends AbstractEnum
{
    /**
     * API key authentication.
     */
    public const API_KEY = 'api_key';
    /**
     * Gets the implementation class for the authentication method.
     *
     * @since 0.4.0
     *
     * @return class-string<RequestAuthenticationInterface&WithArrayTransformationInterface> The implementation class.
     *
     * @phpstan-ignore missingType.generics
     */
    public function getImplementationClass(): string
    {
        // At the moment, this is the only supported method.
        // Once more methods are available, add conditionals here for each method.
        return ApiKeyRequestAuthentication::class;
    }
}
